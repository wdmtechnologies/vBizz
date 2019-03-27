package com.example.amjadkhan.geofence.trip;


interface OnTripDeleteListener {

    void onTripDeleteSuccess(Trip trip);
    void onTripDeleteFailed(String error);
}