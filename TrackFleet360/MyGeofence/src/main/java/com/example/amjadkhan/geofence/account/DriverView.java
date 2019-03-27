package com.example.amjadkhan.geofence.account;


import com.example.amjadkhan.geofence.Employee;

import java.util.List;

public interface DriverView {

     void onEmployeeFetchedSuccess(List<Employee> employees);
     void onEmployeeFetchFailed();

}
