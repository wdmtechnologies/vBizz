package com.example.amjadkhan.geofence.trip;

import android.util.Log;

import com.example.amjadkhan.geofence.account.Api;
import com.example.amjadkhan.geofence.utils.TripServiceApi;

import java.util.List;

import io.reactivex.Observable;
import io.reactivex.android.schedulers.AndroidSchedulers;
import io.reactivex.disposables.Disposable;
import io.reactivex.schedulers.Schedulers;
import retrofit2.Retrofit;

public class TripsFragmentPresenter {

    private static final String TAG = "TripsFragmentPresenter";
     int tripId;
      private Retrofit retrofit;
     private OnTripDeleteListener deleteListener;
     TripFetchListener tripFetchListener;
     TripServiceApi tripServiceApi;
     Trip currenttrip;
     TripsFragmentView view;


      TripsFragmentPresenter(TripsFragmentView view, Retrofit retrofit) {
          this.view = view;
          tripServiceApi = retrofit.create(TripServiceApi.class);
     }

    void fetchTrips(String adminId){
        Log.d(TAG, "fetchTrips: ");

        Observable<List<Trip>> tripsObservable = tripServiceApi.getAllTrips(adminId).subscribeOn(Schedulers.io())
                                                 .observeOn(AndroidSchedulers.mainThread());

        Disposable disposable = tripsObservable.subscribe(this::handleTripsFetchResponse, this::handleTripsFetchError);

    }

    private void handleTripsFetchResponse(List<Trip> trips) {
        Log.d(TAG, "handleTripFetchResponse: "+ trips);
        view.onTripsFetchSuccess(trips);
    }

    private void handleTripsFetchError(Throwable error) {
        Log.d(TAG, "handleTripFetchError: "+ error);
        view.onTripsFetchError();
    }











    void deleteTrip( Trip trip  ,OnTripDeleteListener listener){
        Log.d(TAG, "deleteTrip: ");
        this.deleteListener = listener;

         currenttrip = trip;
         Observable<String> deleteObserv = tripServiceApi.deleteTrip(trip.getId());
//
        deleteObserv.subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread()).subscribe(this::handleDeleteTripSuccess, this::handleDeleteTripError);

     }

    private void handleDeleteTripSuccess(String id) {
        deleteListener.onTripDeleteSuccess(currenttrip);
        Log.d(TAG, "handleResults: "+id);


     }
    private void handleDeleteTripError(Throwable error) {
        Log.d(TAG, "handleError: "+error);
        deleteListener.onTripDeleteFailed(error.toString());

    }





    public void getTripById(String id) {
        Log.d(TAG, "getTripById: ");
        Observable<Trip> tripObserv = tripServiceApi.getTripById(id);
        tripObserv.subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread()).subscribe(this::handleTripResults, this::handleTripError);

    }

    private void handleTripError(Throwable throwable) {
    }

    private void handleTripResults(Trip trip) {

         if (trip != null) {

//             tripFetchListener.on(trip);

         }
    }


    public void getTripByEmployeeId(String empId) {
        Observable<Trip> tripObserv = tripServiceApi.getTripById(empId);

    }
}
