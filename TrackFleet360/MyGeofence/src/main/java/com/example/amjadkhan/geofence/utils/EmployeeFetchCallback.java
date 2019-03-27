package com.example.amjadkhan.geofence.utils;

import com.example.amjadkhan.geofence.Employee;

import java.util.List;

public interface EmployeeFetchCallback {

    void onEmployeeFetchSuccess(List<Employee> trips);
    void onEmployeesFetchError();
}
