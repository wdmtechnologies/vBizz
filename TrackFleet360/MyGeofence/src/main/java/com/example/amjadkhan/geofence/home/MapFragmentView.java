package com.example.amjadkhan.geofence.home;

import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.trip.Trip;

import java.util.List;

public interface MapFragmentView {

    void onEmployeesFetchSuccess(List<Employee> employeeList);
    void onTripsFetchSuccess(List<Trip> tripList);
    void onTripsFetchError();
    void onEmployeesFetchError();

}
