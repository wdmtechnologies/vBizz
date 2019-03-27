package com.example.amjadkhan.geofence.dagger;

import com.example.amjadkhan.geofence.SearchResultActivity;
import com.example.amjadkhan.geofence.home.MapFragment;
import com.example.amjadkhan.geofence.account.DriverListActivity;
import com.example.amjadkhan.geofence.login.LoginActivity;
import com.example.amjadkhan.geofence.trip.AddNewTripActivity;
import com.example.amjadkhan.geofence.trip.TripDetailActivity;
import com.example.amjadkhan.geofence.trip.TripsFragment;
import com.example.amjadkhan.geofence.trip.UpdateTripActivity;

import javax.inject.Singleton;

@Singleton
@dagger.Component(modules = {NetModule.class,AppModule.class})
public interface Component {

    void inject(TripsFragment tripsFragment);
    void inject(DriverListActivity driverListActivity);
    void inject(LoginActivity activity);
    void inject(AddNewTripActivity activity);
    void inject(UpdateTripActivity activity);
     void inject(SearchResultActivity activity);
    void inject(MapFragment mapFragment);
    void inject(TripDetailActivity tripDetailActivity);
  }
