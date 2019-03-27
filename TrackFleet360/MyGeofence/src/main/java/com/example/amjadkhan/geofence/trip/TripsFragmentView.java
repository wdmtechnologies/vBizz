package com.example.amjadkhan.geofence.trip;

import java.util.List;

public interface TripsFragmentView {

    void onTripsFetchSuccess(List<Trip> tripListi);
    void onTripsFetchError();
}
