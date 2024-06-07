<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquationVariable;

class Question extends Model {

  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    "test_id",
    "type",
    "title",
    "description",
    "button_label",
    "order",
    "is_multi_select",
    "is_skippable",
    "show_policy",
    "image",
    "video",
  ];

  public function test() {
    return $this->belongsTo(Test::class);
  }

  public function answers() {
    return $this->hasMany(Answer::class);
  }

  public function testEntries() {
    return $this->hasMany(TestEntry::class);
  }

  public function equationVariables() {
    return $this->hasMany(EquationVariable::class);
  }

  public function largest_order() {
    $order = Question::selectRaw('max(`order`) AS largest_order')->where('test_id', '=', $this->test_id)->first();
    if (isset($order->largest_order) && !empty($order->largest_order)) {
      return $order->largest_order;
    } else {
      return 1;
    }
  }

  public function fields() {
    return $this->hasMany(Field::class);
  }

  public function fields_with_options() {
    return $this->hasMany(Field::class)->with("options");
  }

  public function image_url() {
    if ($this->image != null) {
      return url("images/questions/" . $this->image);
    }
    return "";
  }

  public static function updateValues(array $values, $where = true) {
    $table = Question::getModel()->getTable();

    $cases = [];
    $ids = [];
    $params = [];

    foreach ($values as $id => $value) {
      $id = (int)$id;
      $cases[] = "WHEN {$id} then ?";
      $params[] = $value;
      $ids[] = $id;
    }

    $ids = implode(',', $ids);
    $cases = implode(' ', $cases);
    // $params[] = Carbon::now();

    if (is_string($where) && strlen($where)) {
      return \DB::update(
        "UPDATE `{$table}` SET `order` = CASE `id` {$cases} END WHERE `id` in ({$ids}) AND WHERE {$where}",
        $params
      );
    } else {
      return \DB::update(
        "UPDATE `{$table}` SET `order` = CASE `id` {$cases} END WHERE `id` in ({$ids})",
        $params
      );
    }
  }
}
