<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler; // Import Symfony DomCrawler

class AffiliatedProducts implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $tries = 2;

  /**
   * Create a new job instance.
   */
  public function __construct() {
    //
  }

  /**
   * Execute the job.
   */
  public function handle(): void {

    $affiliate_products = Product::active()->isAffiliate()->get();

    if ($affiliate_products->count() > 0) {
      foreach ($affiliate_products as $product) {
        $json_data = $this->aliexpress_product_extract($product);
        if ($json_data) {
          Log::info($json_data);
          $data = json_decode($json_data);
          if ($data->price != null) {
            $product->price = $data->price;
          }
          if ($data->quantity != null) {
            $product->stock = $data->quantity;
          }
          $product->save();
          Log::info(json_encode(["updated affiliate: ", "id" => $product->id, "price" => $product->price, "quantity" => $product->stock]));
        } else {
          Log::warning(json_encode(["failed to update affiliate: ", "id" => $product->id, "url" => $product->product_link()]));
        }
      }
    }
  }

  private function aliexpress_product_extract(Product $product) {
    // Validate if the URL is an AliExpress product page
    if (!$product->is_from_aliexpress()) {
      return false;
    }

    // Create a new GuzzleHttp client
    $client = new Client();

    // Make a request to the external website
    $response = $client->get($product->product_link());

    // Extract the HTML content from the response
    $html = $response->getBody()->getContents();

    // Parse the HTML using Symfony DomCrawler
    $crawler = new Crawler($html);

    // Find the script tag containing the runParams variable
    $scriptTag = $crawler->filterXPath('//script[contains(text(), "window.runParams")]');

    // Extract the JavaScript code containing the runParams variable
    $jsCode = $scriptTag->text();

    $jsCode = preg_replace('/(\bdata\b)/', '"$1"', $jsCode, 1);

    // Extract the value of the runParams variable
    preg_match('/window\.runParams\s*=\s*({.*?});/', $jsCode, $matches);
    $runParamsJson = $matches[1] ?? null;

    // Decode the JSON data
    $runParams = null;
    if (!empty($runParamsJson)) {
      $runParams = json_decode($runParamsJson, true);
      $runParams = $runParams["data"];

      $productInfo = [];

      // $toOriginalPriceCurrencyRate = $runParams["currencyComponent"]["currencyRate"];// selected currency in aliexpress Rate

      // product original price => priceComponent->origPrice->minPrice;
      $productInfo["price"] = $runParams["priceComponent"]["origPrice"]["minMultiCurrencyPrice"];

      // product discounted price => priceComponent->discountPrice->minActMultiCurrencyPrice;
      $productInfo["discounted_price"] = $runParams["priceComponent"]["discountPrice"]["minActMultiCurrencyPrice"];

      // quantity => inventoryComponent->totalAvailQuantity
      $productInfo["quantity"] = $runParams["inventoryComponent"]["totalAvailQuantity"];

      // discount check => promotionComponent->discountPromotion
      if ($runParams["promotionComponent"]["discountPromotion"]) {
        // discount value => promotionComponent->discount
        $productInfo["discoun"] = $runParams["promotionComponent"]["discount"];
      }

      return json_encode($productInfo);
    }

    return false;
  }
}
