<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class CertificateController extends Controller {

  public function index() {
    $certificates = Certificate::where("status", 1)->get();
    return view("certificates.index", compact("certificates"));
  }

  public function create() {
    $demo = self::demo_data();
    $themes = Certificate::get_themes();
    return view('dashboard.certificate', compact("themes", "demo"));
  }

  public function store(Request $request) {
    $request->validate([
      "title" => "required|string",
      "description" => "nullable|string",
      "price" => "nullable|integer|min:0",
      "status" => "required|integer|min:0|max:1",
      "theme" => "required|string|in:" . implode(",", Certificate::get_themes()),
    ]);

    $res = Certificate::create([
      "title" => request("title"),
      "description" => request("description"),
      "user_id" => auth()->user()->id,
      "price" => request("price"),
      "template" => request("theme"),
      "status" => request("status"),
    ]);

    session()->flash("certificate_added", intval($res->id));

    return redirect()->route("certificates_manage");
  }

  public function show(Certificate $certificate) {

    return view('certificates/' . $certificate->template, ["data" => [
      'user' => auth()->user()->fullname(),
      'title' => $certificate->title,
      'description' => $certificate->description,
    ]]);
  }

  public function show_api(Certificate $certificate) {
    if (auth()->user()->is_admin() || auth()->user()->has_certificate($certificate->id)) {
      return $certificate;
    }
    return abort(404);
  }

  public function download(Certificate $certificate) {
    if (auth()->user()->has_certificate($certificate->id)) {
      $pdf = Pdf::loadView('certificates/' . $certificate->template, ["data" => [
        'user' => auth()->user()->fullname(),
        'title' => $certificate->title,
        'description' => $certificate->description,
      ]]);

      $pdf->setPaper('A4', 'landscape');

      // return $pdf->download();
      // dd($certificate);
      return $pdf->stream();
    } else {
      return abort(404);
    }
  }

  public function show_theme($theme) {

    return view('certificates/' . $theme, ["data" => self::demo_data()]);

    // $pdf = Pdf::loadView('certificates/' . $theme, ["data" => [
    //   'user' => auth()->user()->fullname(),
    //   'description' => 'For deftly defying the laws of gravity<br/>and flying high',
    // ]]);

    // $pdf->setPaper('A4', 'landscape');

    // // return $pdf->download();
    // // dd($certificate);
    // return $pdf->stream();
  }

  public function edit(Certificate $certificate) {
    $demo = self::demo_data();
    $themes = Certificate::get_themes();

    return view('dashboard.certificate', compact("themes", "certificate", "demo"));
  }

  public function update(Request $request, Certificate $certificate) {
    $request->validate([
      "title" => "required|string",
      "price" => "nullable|integer|min:0",
      "status" => "required|integer|min:0|max:1",
      "theme" => "required|string|in:" . implode(",", Certificate::get_themes()),
    ]);

    $certificate->title = request("title");
    $certificate->price = request("price");
    $certificate->template = request("theme");
    $certificate->status = request("status");
    $res = $certificate->save();

    session()->flash("certificate_edited", $res);

    return redirect()->route("certificates_manage");
  }

  public function update_api(Request $request, Certificate $certificate) {
    $request->validate([
      "ar_title" => "required|string",
      "ar_desc" => "nullable|string",
      "price" => "nullable|integer|min:0",
      "status" => "nullable|integer|min:0|max:1",
      "theme" => "required|string|in:" . implode(",", Certificate::get_themes()),
    ]);

    $certificate->title = request("ar_title");
    $certificate->description = request("ar_desc");
    $certificate->template = request("theme");

    if (request("price") != null) {
      $certificate->price = request("price");
    }
    if (request("status") != null) {
      $certificate->status = request("status");
    }

    $res = $certificate->save();

    return ["status" => $res];
  }

  public function destroy(Certificate $certificate) {
    if (auth()->user()->have_created_certificate($certificate->id)) {
      $res = $certificate->delete();
    }
    return ["status" => $res ?? false];
  }

  public static function demo_data() {
    return [
      "title" => "Certificate of Completion",
      "user" => auth()->user()->fullname(),
      "description" => 'For deftly defying the laws of gravity<br/>and flying high',
    ];
  }
}
