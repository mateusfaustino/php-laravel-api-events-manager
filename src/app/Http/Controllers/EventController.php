<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Event::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'title'=>'required',
            'city'=>'required',
            'description'=>'required',
            'private'=>'required'
        ]);
        
        $event = new Event();
        $event->title = $request->title;
        $event->description = $request->description;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->items = $request->items;
        $event->user_id = $user->id;
        
        if($request->hasFile('image') && $request->file('image')->isValid()){
            $requestImage = $request->image;
            $path = $requestImage->store("events",'public');
            $event->image = $path;
        }else{
            $event->image = 'null';
        }

        
        $event->save();
        return $event;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $event=Event::find($id);
        $eventOwner = user::where('id','=',$event->user_id)->first()->toArray();
        $return = (object) [
            'event' => $event,
            'eventOwner' => $eventOwner,
        ];
        
        return $return;
    }

    public function search($title)
    {
        return Event::where('title','like','%'.$title.'%')->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        $event->update($request->all());
        return $event;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Event::destroy($id);
    }
}
