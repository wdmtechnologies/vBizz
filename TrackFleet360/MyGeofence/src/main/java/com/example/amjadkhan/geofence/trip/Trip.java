package com.example.amjadkhan.geofence.trip;

import android.util.Log;

 import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.Vehicle;
import com.google.android.gms.maps.model.LatLng;
import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class Trip  {

        private static final String TAG = "Trip";
        @SerializedName("trip_id")
        @Expose
        private String tripId;

        @SerializedName("trip_name")
        @Expose
        private String name;

        @SerializedName("source_address")
        @Expose
        private String sourceAdrs;

        @SerializedName("desti_address")
        @Expose
        private String destAdrs;

        @SerializedName("leave_time")
        @Expose
        private String pickupTime;

        @SerializedName("drop_time")
        @Expose
        private String dropTime;

        @Expose
        @SerializedName("emp_id")
        private String emp_id;

        @Expose
        @SerializedName("trip_status")
        private String tripStatus;

        @SerializedName("latlngs")
        private List<LatLong> latLngs;


        @SerializedName("current_latlng")
        private LatLong currentLatLng;


        @SerializedName("employee")
        private Employee employee;

        @SerializedName("vehicle")
        private Vehicle vehicle;

        @SerializedName("vehicle_id")
        private String vehicleId;

        @SerializedName("created_by")
        private String adminId;

        @SerializedName("srcLat")
        Double srcLat;

        @SerializedName("srcLng")
        Double srcLng;




    public Trip(String name,
                String sourceAdrs,
                String destAdrs,
                String pickupTime,
                String empId,
                String adminId,Double srcLat, Double srcLng) {
        this.name = name;
        this.sourceAdrs = sourceAdrs;
        this.destAdrs = destAdrs;
        this.pickupTime = pickupTime;
        this.emp_id = empId;
        this.adminId = adminId;
         this.srcLat = srcLat;
        this.srcLng = srcLng;
    }

    public Double getSrcLat() {
        return srcLat;
    }

    public Double getSrcLng() {
        return srcLng;
    }

    public String getId() {
            return tripId;
        }

        public void setId(String id) {
            Log.d(TAG, "setId: ");
            this.tripId = id;
        }

    public String getTripStatus() {
        return tripStatus;
    }

    public String getEmp_id() {
        return emp_id;
    }


    public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public String getSourceAdrs() {
            return sourceAdrs;
        }

        public void setSourceAdrs(String sourceAdrs) {
            this.sourceAdrs = sourceAdrs;
        }

        public String getDestAdrs() {
            return destAdrs;
        }

        public void setDestAdrs(String destAdrs) {
            this.destAdrs = destAdrs;
        }

        public String getPickupTime() {
            return pickupTime;
        }

        public void setPickupTime(String pickupTime) {
            this.pickupTime = pickupTime;
        }

        public String getDropTime() {
            return dropTime;
        }

        public void setDropTime(String dropTime) {
            this.dropTime = dropTime;
        }



    public void setDriverName(String driverName) {
            this.emp_id = driverName;
        }

    public void setEmp_id(String emp_id) {
        this.emp_id = emp_id;

    }


    public Employee getEmployee() {
        return employee;
    }

    public Vehicle getVehicle() {
        return vehicle;
    }

    public String getVehicleId() {
        return vehicleId;
    }

    public String getAdminId() {
        return adminId;
    }

    public LatLng getCurrentLatLng() {
        return new LatLng(currentLatLng.getLat(),currentLatLng.longitude);
    }

    public List<LatLong> getLatLngs() {
        return latLngs;
    }
}

