package com.example.amjadkhan.geofence.account;

import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Color;
import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.utils.TripServiceApi;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import de.hdodenhof.circleimageview.CircleImageView;

public class DriverListRecyclerAdapter extends RecyclerView.Adapter<DriverListRecyclerAdapter.CardViewHolder> {

    private Context context;

    private ItemClickListener listener;
     List<Employee> driversList;
    TypedArray driver_img;
    String[] driver_name;
    String[] vehicleno;
     private static final String TAG = "DriverListRecycler";

    public interface ItemClickListener {
        void onItemClick(int position);

    }

    public void addDrivers(List<Employee> drivers) {
        if (!drivers.isEmpty()) {
            driversList.clear();
            driversList.addAll(drivers);
            notifyDataSetChanged();
            Log.d(TAG, "addDrivers: "+drivers);
         }

    }

    public DriverListRecyclerAdapter(ItemClickListener listener,Context context) {
          driversList = new ArrayList<>();
          this.context = context;
          this.listener = listener;

    }

    @NonNull
    @Override
    public CardViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
       return new CardViewHolder(LayoutInflater.from(parent.getContext()).inflate(R.layout.item_driver,null,false));

    }

    @Override
    public void onBindViewHolder(CardViewHolder holder, int position) {
        Employee driver = driversList.get(position);
        holder.driver_name.setText(driver.geteName());
        Picasso.get().load(TripServiceApi.IMG_BASE_URL+driver.geteImg()).into(holder.imageView);

//        if (driver.getOnlineStatus().equals("1")) {
////               holder.driver_status.setText("Online");
////               holder.driver_status.setTextColor(Color.parseColor("#00FF00"));
////        }
////        else{
////            holder.driver_status.setText("Offline");
////
////        }
     }


    @Override
    public int getItemCount() {
        return driversList.size();
    }

     class CardViewHolder extends RecyclerView.ViewHolder {

        private static final String TAG = "CardViewHolder";
        @BindView(R.id.parent)
        RelativeLayout parentLayout;
        @BindView(R.id.iv_item_driver_img)
         CircleImageView imageView;
        @BindView(R.id.tv_item_driver_name)
        TextView driver_name;
        @BindView(R.id.tv_item_driver_status)
        TextView driver_status;
        @BindView(R.id.tv_item_driver_vehcile_no)
        TextView vehicle_no;


        CardViewHolder(final View itemView) {
          super(itemView);
            Log.d(TAG, "CardViewHolder: "+this);
            ButterKnife.bind(this,itemView);


            parentLayout.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    listener.onItemClick(getAdapterPosition());
                    Log.d(TAG, "onClick: ");
                 }
            });










      }
  }
}
