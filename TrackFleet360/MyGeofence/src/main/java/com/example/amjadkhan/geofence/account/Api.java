package com.example.amjadkhan.geofence.account;

 import com.example.amjadkhan.geofence.Employee;
 import com.example.amjadkhan.geofence.Vehicle;

import java.util.List;

import io.reactivex.Observable;
import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.GET;
import retrofit2.http.POST;

public interface Api {

      String BASE_URL = "https://trackfleet360.com/app/fleet/admin/";

    @FormUrlEncoded
    @POST("get_drivers.php")
    Observable<List<Employee>> getEmployee(@Field("admin_id")String adminId);

    @FormUrlEncoded
    @POST("get_vehicles.php")
    Observable<List<Vehicle>> getVehicles(@Field("admin_id")String adminId);






    @FormUrlEncoded
    @POST("delete_trip.php")
    Observable<String> deleteTrip(@Field("id") int tripId, @Field("emp_id") String empId);


    @FormUrlEncoded
    @GET("get_admin.php")
    Call<ProfileFragment.Admin> getAdmin(@Field("email")String email);
}
