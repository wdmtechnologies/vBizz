package com.example.amjadkhan.geofence;

import android.arch.persistence.room.ColumnInfo;
import android.os.Parcel;
import android.os.Parcelable;

import com.google.gson.annotations.SerializedName;

public class Vehicle {

    @SerializedName("vehicle_id")
    private String id;

    @SerializedName("v_name")
    private String vehicleName;

    @SerializedName("plate_no")
    private String plateNo;

    public String getId() {
        return id;
    }

    public String getPlateNo() {
        return plateNo;
    }

    public String getVehicleName() {
        return vehicleName;
    }
}
