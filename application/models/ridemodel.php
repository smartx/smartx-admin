<?php
class RideModel extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

        function getWaitingRidesForTDriver($tdriverId){
		$sql = "SELECT tr.ride_id , ua.lat as pickup_lat, ua.lng as pickup_lng, u.first_name, u.phone_number as phone, ua.address, ua.reference
			FROM taxi_rides tr 
                        JOIN users u using(user_id)
			JOIN user_addresses ua on(tr.origin_address_id=ua.address_id) WHERE tr.status = 1 
                        AND ride_id NOT IN (SELECT rrp.ride_id FROM ride_request_polls rrp WHERE rrp.tdriver_id=?) ORDER BY 1";
		$query = $this->db->query($sql, array($tdriverId));
		return $query->result();
	}
        
        function createInitialRequestPoll($rideId, $tdriverIdArray){
		$N = count($tdriverIdArray);
		if($N==0) return;

		$sql = "INSERT INTO ride_request_polls(ride_id, tdriver_id, status, reg_date)
			VALUES";
		
		for($i=0;$i<$N;$i++){
			$sql.=" (".$rideId.", ?, 1, NOW())";
			if($i<$N-1){
				$sql.=',';	
			}
		}
		$query = $this->db->query($sql,$tdriverIdArray);
                return $query->db->affected_rows();
	}
        function addTDriverToRidePoll($tdriverId, $rideIdArray){
            	$N = count($rideIdArray);
		if($N==0) return;

		$sql = "INSERT INTO ride_request_polls(ride_id, tdriver_id, status, reg_date)
			VALUES";
		
		for($i=0;$i<$N;$i++){
			$sql.=" (?, '".$tdriverId."', 1, NOW())";
			if($i<$N-1){
				$sql.=',';
			}
		}
		$query = $this->db->query($sql,$rideIdArray);
                return $this->db->affected_rows();
        }
        
        

}