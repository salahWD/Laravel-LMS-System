<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\AffiliatedProducts;

class DispatchAffiliatedProducts extends Command {

  protected $signature = 'affiliated-products:dispatch';
  protected $description = 'Dispatch AffiliatedProducts Queue job';

  public function handle() {
    dispatch(new AffiliatedProducts());
    $this->info('AffiliatedProducts job dispatched successfully!');
  }
}
