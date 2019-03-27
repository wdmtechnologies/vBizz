package com.example.amjadkhan.geofence.trip;


import android.content.Context;
import android.graphics.Color;
import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.utils.TripServiceApi;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import de.hdodenhof.circleimageview.CircleImageView;


public class TripRecyclerAdapter extends RecyclerView.Adapter<TripRecyclerAdapter.TripViewHolder> {
    private TripClickListener listener;
    private static final String TAG = "TripRecyclerAdapter";
    Context context;

    List<Trip> tripsList = new ArrayList<>();



    //Callback defination for handling click events on trip item
    public interface TripClickListener {
        void onTripPopupMenuButtonClicked(Trip trip, View view);
        void onTripClicked(Trip trip);
    }


      TripRecyclerAdapter(TripClickListener listener) {
       this.listener = listener;
     }



      void addTrips(List<Trip> trips) {
        tripsList.clear();
        tripsList.addAll(trips);
        notifyDataSetChanged();
    }


    @NonNull
    @Override
    public TripViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        context = parent.getContext();
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_trip,parent,false);
        return new TripViewHolder(view);
    }


    @Override
    public void onBindViewHolder(TripViewHolder holder, int position) {

             Trip trip = tripsList.get(position);
             holder.trip_name.setText(trip.getName());
            Picasso.get().load(TripServiceApi.IMG_BASE_URL+trip.getEmployee().geteImg()).into(holder.driverImg);
        if (trip.getTripStatus().equals("1")){
            holder.tripStatus.setText("Online");
            holder.tripStatus.setTextColor(Color.parseColor("#00FF00"));
        }
        else{
            holder.tripStatus.setText("Offline");
            holder.tripStatus.setTextColor(Color.parseColor("#d3d3d3"));
        }



    }

    @Override
    public int getItemCount() {
        return tripsList.size();
    }



    class TripViewHolder extends RecyclerView.ViewHolder {

        @BindView(R.id.tv_item_trip_name)
        TextView trip_name;
        @BindView(R.id.iv_driver)
        CircleImageView driverImg;
        @BindView(R.id.trip_status)
        TextView tripStatus;

        @BindView(R.id.btn_show_popup)
        ImageView more_iv;

          TripViewHolder(View itemView) {
            super(itemView);
            ButterKnife.bind(this,itemView);
        }


        @OnClick({R.id.btn_show_popup,R.id.item_parent})
        public void onClick(View view) {
            if (view.getId() == R.id.btn_show_popup) {
                listener.onTripPopupMenuButtonClicked(tripsList.get(getAdapterPosition()), view);

            }
            else {
               listener.onTripClicked(tripsList.get(getAdapterPosition()));
            }
        }


    }



    public void deleteTrip(Trip trip) {
        Log.d(TAG, "deleteTrip: ");
        tripsList.remove(trip);
        notifyDataSetChanged();
    }


    public void addTrip(Trip trip) {
        Log.d(TAG, "addTrip: ");
        tripsList.add(trip);
        notifyItemInserted(tripsList.size());
     }

    public void addTrip(Trip trip, int position) {
        Log.d(TAG, "addTrip: ");
        tripsList.add(position,trip);
        notifyDataSetChanged();
    }


    public List<Trip> getTripsList() {
        return tripsList;
    }
}

