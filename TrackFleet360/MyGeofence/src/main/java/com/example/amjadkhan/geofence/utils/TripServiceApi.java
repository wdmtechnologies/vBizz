package com.example.amjadkhan.geofence.utils;

import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.trip.Trip;

import java.util.List;

import io.reactivex.Observable;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;
import retrofit2.http.Path;

public interface TripServiceApi {

    String BASE_URL = "http://trackfleet360.com/app/admin/";
        String IMG_BASE_URL = "https://trackfleet360.com/app/upload/driver/";

    @FormUrlEncoded
    @POST("live_employees")
    Observable<List<Employee>> getEmployees(@Field("admin_id")String adminId);


        @FormUrlEncoded
    @POST("insert_trip.php")
    Observable<String> insertNewTrip(@Field("trip_name") String name
            , @Field("source_address")String sourceAddress
            , @Field("desti_address") String destAdrs
            , @Field("leave_time") String pickupTime
            , @Field("emp_id") String empId
             ,@Field("created_by")String adminId
            ,@Field("srcLat")Double srcLat
            ,@Field("srcLng")Double srcLng);


    @POST("insert_trip.php")
    Observable<String>  insertNewTrip(@Body Trip trip);


    @FormUrlEncoded
    @POST("update_trip.php")
    Call<String>  updateTrip(@Field("trip_id")String tripId,@Field("trip_name") String name
            , @Field("source_address")String sourceAddress
            , @Field("desti_address") String destAdrs
            , @Field("leave_time") String pickupTime
            , @Field("emp_id") String empId);





    @FormUrlEncoded
    @POST("trips/{trip_id}")
    Observable<Trip> getTripById(@Field("trip_id") String id);

    @FormUrlEncoded
    @POST("delete_trip.php")
    Observable<String> deleteTrip(@Field("trip_id") String tripId);


    @FormUrlEncoded
    @POST("live_trips/{trip_id}")
    Observable<Trip> getOnTripsById(@Path("trip_id")String tripId, @Field("admin_id") String adminId);

    @FormUrlEncoded
    @POST("live_trips")
    Observable<List<Trip>> getAllOnTrips(@Field("admin_id") String adminId);


    @FormUrlEncoded
    @POST("trips")
    Observable<List<Trip>> getAllTrips(@Field("admin_id") String adminId);





}
