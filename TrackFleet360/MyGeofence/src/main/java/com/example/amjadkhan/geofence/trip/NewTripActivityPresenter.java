package com.example.amjadkhan.geofence.trip;

import android.util.Log;
import android.view.View;

import com.example.amjadkhan.geofence.BasePresenter;
import com.example.amjadkhan.geofence.BaseView;
import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.Vehicle;
import com.example.amjadkhan.geofence.account.Api;
import com.example.amjadkhan.geofence.account.Driver;
import com.example.amjadkhan.geofence.utils.Session;
import com.example.amjadkhan.geofence.utils.TripServiceApi;

import java.util.List;

import javax.inject.Inject;

import io.reactivex.Observable;
import io.reactivex.android.schedulers.AndroidSchedulers;
import io.reactivex.disposables.Disposable;
import io.reactivex.schedulers.Schedulers;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class NewTripActivityPresenter extends BasePresenter<NewTripActivityView> {

    private static final String TAG = "NewTripActivityPresente";
    private TripServiceApi api;
    NewTripActivityView view;



      NewTripActivityPresenter(NewTripActivityView view,Retrofit retrofit) {
        super(view);
        api = retrofit.create(TripServiceApi.class);
     }


     void addNewTrip(final Trip trip){
        Log.d(TAG, "addNewTrip: ");

        Observable<String> newTripObserv = api.insertNewTrip(trip);
        newTripObserv.subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread()).subscribe(this::handleAddTripResult, this::handleAddTripError);

    }

    private void handleAddTripResult(String tripId) {
        Log.d(TAG, "handleAddTripResult: ");
            getView().onTripAddSuccess(tripId);
         }


    private void handleAddTripError(Throwable error) {
        Log.d(TAG, "handleAddTripError: "+error.toString());
        getView().onTripAddFailed();
    }

    public void fetchEmployees(String adminId){
        Log.d(TAG, "getDrivers: ");

        Observable<List<Employee>> tripsObservable = api.getEmployees(adminId);
        Disposable subscribe = tripsObservable.subscribeOn(Schedulers.io())
                              .observeOn(AndroidSchedulers.mainThread())
                              .subscribe(this::handleEmployeeFetchResponse, this::handleEmployeeFetchError);

    }



    private void handleEmployeeFetchResponse(List<Employee> employees) {
        Log.d(TAG, "handleEmployeeFetchResponse: 71"+ employees);
         if (employees != null && !employees.isEmpty()) {
             Log.d(TAG, "handleEmployeeFetchResponse: 73");
            view.onEmployeeFetchedSuccess(employees);
         }

    }

    private void handleEmployeeFetchError(Throwable error) {
        Log.d(TAG, "handleEmployeeFetchError: "+error.toString());
        view.onEmployeeFetchFailed();
    }

}
