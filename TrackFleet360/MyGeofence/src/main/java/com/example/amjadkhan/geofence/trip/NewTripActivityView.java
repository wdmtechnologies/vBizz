package com.example.amjadkhan.geofence.trip;

import com.example.amjadkhan.geofence.BaseView;
import com.example.amjadkhan.geofence.Employee;

import java.util.List;

public interface  NewTripActivityView extends BaseView {

    void onTripAddSuccess(String  tripId);
    void onTripAddFailed();
    void onEmployeeFetchedSuccess(List<Employee> employees);
    void onEmployeeFetchFailed();
}
