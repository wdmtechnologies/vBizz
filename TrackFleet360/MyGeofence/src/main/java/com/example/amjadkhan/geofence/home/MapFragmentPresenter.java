package com.example.amjadkhan.geofence.home;

import android.util.Log;

import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.trip.Trip;
import com.example.amjadkhan.geofence.utils.TripServiceApi;

import java.util.List;

import io.reactivex.Observable;
import io.reactivex.android.schedulers.AndroidSchedulers;
import io.reactivex.disposables.Disposable;
import io.reactivex.schedulers.Schedulers;
import retrofit2.Retrofit;

public class MapFragmentPresenter {

    private static final String TAG = "MapFragmentPresenter";
    private TripServiceApi api;
    private MapFragmentView view;

    
     MapFragmentPresenter(MapFragmentView view, Retrofit retrofit) {
         Log.d(TAG, "MapFragmentPresenter: 27");
          api = retrofit.create(TripServiceApi.class);
          this.view = view;
    }

    
     void fetchLiveEmployees(String adminId){
        Log.d(TAG, "fetchEmployees: ");

        Observable<List<Employee>> employeesObservable = api.getEmployees(adminId).subscribeOn(Schedulers.io())
                                                 .observeOn(AndroidSchedulers.mainThread());

         Disposable disposable = employeesObservable.subscribe(this::handleEmployeeResponse, this::handleEmployeeError);


     }

    private void handleEmployeeResponse(List<Employee> employees) {
        Log.d(TAG, "handleEmployeeResponse: 42");
        if (employees != null) {
            Log.d(TAG, "handleEmployeeResult: 40 "+ employees);
            view.onEmployeesFetchSuccess(employees);
         }
         
         else{
            Log.d(TAG, "handleEmployeeResult: trips null");
            view.onEmployeesFetchError();
        }
         
        
      }

    private void handleEmployeeError(Throwable error) {
        Log.d(TAG, "handleEmployeeError: "+error.toString());
        view.onEmployeesFetchError();
    }


//    public void fetchOnTrip(String tripId, String adminId) {
//        Observable<Trip> employeesObservable = api.getOnTripsById(tripId,adminId).subscribeOn(Schedulers.io())
//                .observeOn(AndroidSchedulers.mainThread());
//
//        Disposable disposable = employeesObservable.subscribe(this::handleTripFetchResponse, this::handleTripFetchError);
//
//    }
//
//    private void handleTripFetchResponse(Trip trips) {
//        Log.d(TAG, "handleTripFetchResponse: ");
//    }
//
//    private void handleTripFetchError(Throwable error) {
//        Log.d(TAG, "handleTripFetchError: ");
//    }


    public void fetchTrips(String adminId) {
        Observable<List<Trip>> employeesObservable = api.getAllOnTrips(adminId).subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread());

        Disposable disposable = employeesObservable.subscribe(this::handleTripsFetchResponse, this::handleTripsFetchError);

    }

    private void handleTripsFetchResponse(List<Trip> trips) {
        Log.d(TAG, "handleTripFetchResponse: "+ trips);
        view.onTripsFetchSuccess(trips);
    }

    private void handleTripsFetchError(Throwable error) {
        Log.d(TAG, "handleTripFetchError: "+ error);
        view.onTripsFetchError();
    }






}
