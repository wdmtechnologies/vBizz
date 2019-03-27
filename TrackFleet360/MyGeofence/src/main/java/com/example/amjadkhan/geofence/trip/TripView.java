package com.example.amjadkhan.geofence.trip;

import java.util.List;

public interface TripView {

    void onTripFetchSuccess(List<Trip> trips);
    void onTripFetchFailed(String error);
}
