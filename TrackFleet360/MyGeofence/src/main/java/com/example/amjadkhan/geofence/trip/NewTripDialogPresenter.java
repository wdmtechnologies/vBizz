package com.example.amjadkhan.geofence.trip;

import android.util.Log;

import com.example.amjadkhan.geofence.utils.TripServiceApi;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class NewTripDialogPresenter {

    private static final String TAG = "NewTripDialogPresenter";
     OnNewTripAddListener view;
      int tripId;
     Retrofit retrofit;


    public NewTripDialogPresenter(OnNewTripAddListener view,Retrofit retrofit) {
        this.view = view;
         this.retrofit = retrofit;
//        tripDao = MyApp.getRoomDb().getTripDao();
//        Log.d(TAG, "NewTripDialogPresenter: "+ tripDao);
    }

   public NewTripDialogPresenter(Retrofit retrofit) {
        this.retrofit = retrofit;
    }



    public interface OnNewTripAddListener {
        void onTripAddSuccess(Trip trip);
        void onTripAddFailed();
    }



    public void deleteTrip(String tripId){
        Log.d(TAG, "deleteTrip: ");
     }




    public List<Trip> getAllTrip(){
        return null;
    }

    public int getTripCount(){
        return 7;
     }




    public void updateTrip(final Trip trip){
        Log.d(TAG, "updateTrip: ");


        TripServiceApi api = retrofit.create(TripServiceApi.class);
        Call<String> call = api.updateTrip(trip.getId(),trip.getName(),trip.getSourceAdrs(),trip.getDestAdrs(),trip.getPickupTime(),trip.getEmp_id());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d(TAG, "onResponse: "+response.code());
                if (response.isSuccessful()) {
                    Log.d(TAG, "onResponse: "+ response.body());
                    if (response.body() != null) {
                        String tripId = response.body();
                        Log.d(TAG, "onResponse tripId : "+tripId);
                        trip.setId(tripId);
                        view.onTripAddSuccess(trip);

                    }
                }
                else{
                    Log.d(TAG, "onResponse: Unsuccess");
                }
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d(TAG, "onFailure: "+t);
            }
        });

    }
}
