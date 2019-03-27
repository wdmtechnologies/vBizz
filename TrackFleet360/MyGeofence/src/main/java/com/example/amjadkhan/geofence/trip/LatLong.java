package com.example.amjadkhan.geofence.trip;

import com.google.android.gms.maps.model.LatLng;
import com.google.gson.annotations.SerializedName;

public class LatLong {

    @SerializedName("lattitude")
    Double latitude;

    @SerializedName("longitude")
    Double longitude;

    public Double getLat() {
        return latitude;
    }

    public Double getLng() {
        return longitude;
    }

    public LatLng getLatLng() {
        return new LatLng(latitude,longitude);
    }
}
