<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;
use App\Http\Requests\LinkRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LinksController extends Controller
{

   /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('links.index', [
            'links' => Auth::user()->links()->orderBy('created_at', 'DESC')->get()
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('links.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\LinkRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LinkRequest $request)
    {
        
        //if links > 20 delete last one
        (Link::count() >= 20) ? Link::latest()->first()->delete() : true;
        
        $linkData = $request->all();
        $linkData['shortcut'] = Str::random(6);
        Auth::user()->links()->create($linkData);
        return redirect()->route('link.index')->with('created',trans('trans.link_created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $shortcut
     * @return \Illuminate\Http\Response
     */
    public function show($shortcut)
    {
        $link = Link::whereShortcut($shortcut)->firstOrFail();
        return redirect($link->link);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $link
     * @return \Illuminate\Http\Response
     */
    public function destroy($link)
    {
        Auth::user()->links()->whereId($link)->firstOrFail()->delete();
        return redirect()->route('link.index')->with('deleted',trans('trans.link_deleted'));

    }
}