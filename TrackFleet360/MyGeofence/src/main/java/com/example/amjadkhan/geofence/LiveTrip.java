package com.example.amjadkhan.geofence;

import com.google.android.gms.maps.model.LatLng;

public class LiveTrip {

    private LatLng latLng;
    private String driverName;
    private String vehicleId;
    private String address;

    public LiveTrip(LatLng latLng,String adddress, String driver_name, String vehicleId) {
        this.latLng = latLng;
        this.driverName = driver_name;
        this.vehicleId = vehicleId;
    }

    public LatLng getLatLng() {
        return latLng;
    }

    public String getDriverName() {
        return driverName;
    }

    public String getVehicleId() {
        return vehicleId;
    }

    public String getAddress() {
        return address;
    }


}
