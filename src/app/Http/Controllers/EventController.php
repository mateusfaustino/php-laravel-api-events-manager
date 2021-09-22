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
        return response(['event'=>$event], 201);
        
    }

    public function show($id)
    {
        $event=Event::find($id);
        $http_code = 200;
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
            $http_code = 404;
        }
        return response(['data'=>$return], $http_code);
    }

    public function search($title)
    {
        $data = Event::where('title','like','%'.$title.'%')->get(); 
        return response(['data'=>$data], 200);
    }


    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        $user = auth()->user();
        $event= Event::findOrFail($id);
        $return = (object)[];
        if($event && $user->id==$event->user_id){
            $event->update($request->all());
            $return->message = "Event updated with success";
            $return->event = $event;
            
        }else{
            $return->message = "You can not upadate this Event. You are not the owner of this event!";
        }
        return $return;
    }
    public function edit(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $user = auth()->user();
        $return = (object)[];
        if($event && $user->id==$event->user_id){
            $return->message = "event for update";
            $return->event = $event;
        }else{
            $return->message = "You can not edit this Event or this event does not exist";
        }
        return $return;
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
