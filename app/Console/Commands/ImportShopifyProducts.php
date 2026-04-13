<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Exception;

class ImportShopifyProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:shopify {file : Path to the Shopify CSV file} {--download-images : Attempt to automatically download product images from Shopify URLs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import 3500+ products securely from a Shopify CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error("The file at '{$filePath}' does not exist.");
            return 1;
        }

        $this->info("Starting Shopify CSV Import process...");
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);
            
            // Clean headers (remove BOM if present inside the first header column)
            $headers = array_map('trim', $headers);
            if (isset($headers[0])) {
                $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
            }

            $this->info("Calculating total rows. This might take a moment...");
            $totalRows = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $totalRows++;
            }
            rewind($handle);
            fgetcsv($handle); // skip headers again

            // Provide a visual progress bar
            $bar = $this->output->createProgressBar($totalRows);
            $bar->start();

            $categoriesMap = []; // cache categories locally to minimize DB queries

            while (($data = fgetcsv($handle)) !== false) {
                // Ensure data array matches the header count
                $row = array_combine($headers, array_pad($data, count($headers), null));

                // Process only the first row of a Handle variant group (Shopify creates multiple rows per handle for variants/images. First log always has the title)
                if (empty($row['Title'])) {
                    $bar->advance();
                    continue; 
                }

                try {
                    // Category mapping
                    $categoryName = !empty($row['Type']) ? $row['Type'] : 'Uncategorized';
                    if (!isset($categoriesMap[$categoryName])) {
                        $category = Category::firstOrCreate([
                            'slug' => Str::slug($categoryName)
                        ], [
                            'name' => $categoryName,
                            'is_active' => true
                        ]);
                        $categoriesMap[$categoryName] = $category->id;
                    }

                    // Assign prices smoothly
                    $price = floatval($row['Variant Price'] ?? 0);
                    $compareAt = floatval($row['Variant Compare At Price'] ?? 0);
                    
                    if ($compareAt > 0 && $compareAt > $price) {
                        $finalPrice = $compareAt;
                        $finalDiscountPrice = $price;
                    } else {
                        $finalPrice = $price;
                        $finalDiscountPrice = null;
                    }

                    // Product Creation or Update
                    $product = Product::updateOrCreate(
                        ['slug' => $row['Handle']],
                        [
                            'title' => $row['Title'],
                            'category_id' => $categoriesMap[$categoryName],
                            'full_description' => $row['Body (HTML)'] ?? null,
                            'description' => strip_tags(Str::limit($row['Body (HTML)'] ?? '', 180)),
                            'author' => !empty($row['Vendor']) ? $row['Vendor'] : 'Unknown Author',
                            'price' => $finalPrice,
                            'discount_price' => $finalDiscountPrice,
                            'isbn' => $row['Variant Barcode'] ?? null,
                            'stock' => intval($row['Variant Inventory Qty'] ?? 50), 
                            'is_active' => strtolower($row['Published'] ?? 'TRUE') === 'false' ? false : true,
                            'is_featured' => false,
                        ]
                    );

                    // Optional Image Downloading
                    if ($this->option('download-images') && !empty($row['Image Src']) && empty($product->cover_image)) {
                        try {
                            $imageUrl = $row['Image Src'];
                            $contents = Http::timeout(10)->get($imageUrl)->body();
                            $name = 'products/' . $product->slug . '-' . uniqid() . '.jpg';
                            Storage::disk('public')->put($name, $contents);
                            $product->update(['cover_image' => $name]);
                        } catch (Exception $e) {
                            // Suppress image download failure so it doesn't break the massive import loop
                        }
                    }

                } catch (Exception $e) {
                    $this->error("\nError importing product '{$row['Title']}': " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            fclose($handle);

            $this->newLine();
            $this->info("Import successfully completed!");
            
            if (!$this->option('download-images')) {
                $this->warn("Note: You ran the import without downloading images. If you wish to download cover images locally from Shopify URLs, append --download-images to your command.");
            }

        } else {
            $this->error("Unable to open the file stream. Please ensure the file has read permissions.");
        }
    }
}
