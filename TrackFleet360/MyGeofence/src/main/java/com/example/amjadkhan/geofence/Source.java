package com.example.amjadkhan.geofence;

import android.arch.persistence.room.ColumnInfo;
import android.os.Parcel;
import android.os.Parcelable;
import android.util.Log;

import com.google.android.gms.maps.model.LatLng;



public class Source implements Parcelable {
    private static final String TAG = "Source";

     @ColumnInfo(name = "srclatlng")
     private LatLng latLng;
     @ColumnInfo(name = "src_address")
    private String address;
     @ColumnInfo(name = "pickup_time")
    private String time;


    public Source(LatLng latLng, String address, String time) {
        Log.d(TAG, "Source: ");
        this.latLng = latLng;
        this.address = address;
        this.time = time;
    }

    protected Source(Parcel in) {
        Log.d(TAG, "Source: ");
        latLng = in.readParcelable(LatLng.class.getClassLoader());
        address = in.readString();
        time = in.readString();
    }

    //The creater instance creates instance of class from parcel data
    public static final Creator<Source> CREATOR = new Creator<Source>() {
        @Override
        public Source createFromParcel(Parcel in) {
            Log.d(TAG, "createFromParcel: ");
            return new Source(in);
        }

        @Override
        public Source[] newArray(int size) {
            return new Source[size];
        }
    };

    public LatLng getLatLng() {
        return latLng;
    }

    public String getAddress() {
        return address;
    }

    public String getTime() {
        return time;
    }


    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        Log.d(TAG, "writeToParcel: ");
        dest.writeParcelable(latLng, flags);
        dest.writeString(address);
        dest.writeString(time);
    }
}
