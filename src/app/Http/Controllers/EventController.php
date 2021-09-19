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

    public function show($id)
    {
        $event=Event::find($id);
        if($event){
            $eventOwner = user::where('id','=',$event->user_id)->first()->toArray();
            $return = (object)[];
            $return = (object) [
                'event' => $event,
                'eventOwner' => $eventOwner,
            ];
        }else{
            $return = (object) [
                'message' => "event not found",
            ];
        }
        
        return $return;
    }

    public function search($title)
    {
        return Event::where('title','like','%'.$title.'%')->get();
    }


    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        $event->update($request->all());
        return $event;
    }
    
    public function destroy($id)
    {
        $user = auth()->user();
        $event= Event::findOrFail($id);
        $message = "";
        if($user->id==$event->user_id){
            $event->delete();
            $message = "Event deleted";
        }else{
            $message = "You can't delete this Event. You are not the owner of this event!";
        }
        $return = (object) [
            'message' => $message
        ];
        return $return;

    }

    public function dashboard(){
        $user = auth()->user();
        $events = $user->events;
        
        return $events;
    }
}
