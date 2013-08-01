<?php
class UserModel extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function getUserById($fbId)
	{	
	// 		$query = "SELECT user_id FROM fb_user_users WHERE fb_id = '".$fbID."'";
 //  	$id = getRow($query);
 //   	return $id;

		$this->db->select('user_id');
		$query = $this->db->get_where('fb_users', array('fb_id' => $fbId));
		return $query->result()->user_id;
	}
        
        public function getUserRideStatusByUserId($userId){
            $ride = $this->getUserRide($userId);
        }
        
        //      BEGIN fetch ride status
	function getUserRide($userId){
		$sql = "SELECT t.ride_id, a.lat, a.lng, t.status, t.request_date
                    FROM taxi_rides t
                    JOIN user_addresses a ON ( a.creator_user_id = t.user_id ) 
                    WHERE creator_user_id = ?
                    AND t.status
                    IN ( 1, 2, 7 )";
		$query = $this->db->query($sql,array($userId));
		return $query->row();
	}
        function getRideDriver($rideId){
		$sql = "SELECT rrp.ride_id, rrp.tdriver_id, 
                                td.name, X( tdll.location ) lat, Y( tdll.location ) lng,
                                td.phone
                        FROM ride_request_polls rrp
                        JOIN tdrivers td
                        USING ( tdriver_id ) 
                        JOIN tdriver_latest_locations tdll
                        USING ( tdriver_id ) 
                        WHERE rrp.ride_id = ?
                        AND rrp.status =2";
		$query = $this->db->query($sql,array($rideId));
		return $query->row();
	}
        //      END fetch ride status
        
	function clearRequestPolls(){
		$sql = "DELETE FROM ride_request_polls";
		$query = $this->db->query($sql);
		$sql = "DELETE FROM taxi_rides";
		$query = $this->db->query($sql);
		return $this->db->affected_rows();
	}

	function getUserLocations(){
		$sql = "SELECT user_id as id, lat, lng FROM user_latest_locations";
		$query = $this->db->query($sql);
		return $query->result();
	}

	function getRideRequestPolls(){
		$sql = "SELECT tr.ride_id , tr.user_id, rrp.tdriver_id, rrp.status , ua.lat as pickup_lat, ua.lng as pickup_lng
			FROM ride_request_polls rrp RIGHT JOIN taxi_rides tr using(ride_id) 
			JOIN user_addresses ua on(tr.origin_address_id=ua.address_id) WHERE rrp.status IN (1,2) OR rrp.status IS NULL ORDER BY 1";
		$query = $this->db->query($sql);
		return $query->result();
	}

	function createNewUser($initialLocation){

		$sql = "INSERT INTO users(first_name, last_name, email, username) VALUES ('same name','same','same@gmail.com','same')";
		$this->db->query($sql);
		$lastInsertId = $this->db->insert_id();
		$sql2 = "INSERT INTO user_latest_locations(user_id, lat, lng, reg_date) VALUES (LAST_INSERT_ID(),?,?,NOW())";
		$this->db->query($sql2, $initialLocation);
		return $lastInsertId;
	}

	function clearUserLocations(){
		$sql="DELETE FROM users";
		$this->db->query($sql);
		$sql="DELETE FROM user_latest_locations";
		$this->db->query($sql);
		return $this->db->affected_rows();
	}

	function startRide($userId, $originAddressId){
		$sql = "INSERT INTO  taxi_rides(user_id, origin_address_id, request_date, status)
			VALUES (?,?, NOW(), 1)";
		$query = $this->db->query($sql,array($userId, $originAddressId));
		$lastInsertId = $this->db->insert_id();

		$origin = $this->getLatLngFromUserAddress($originAddressId);
		// print_r($origin);
		$sql2 = "INSERT INTO  ride_events(ride_id, lat, lng, type, reg_date)
			VALUES (?,?,?, 1,NOW())";
		$this->db->query($sql2,array($lastInsertId, $origin->lat, $origin->lng));
		return (object)array('rideId'=>$lastInsertId,'lat'=>$origin->lat, 'lng'=>$origin->lng);
	}

	function updateUserLocation($userId, $lat, $lng){

		$sql = "INSERT INTO user_location_histories (user_id, lat, lng, reg_date) VALUES (?,?,?,NOW())";
		$query = $this->db->query($sql,array($userId, $lat, $lng));

		$sql = "UPDATE user_latest_locations SET lat =?, lng=?, reg_date = NOW() WHERE user_id = ?";
		$query = $this->db->query($sql, array($lat, $lng, $userId));
		return $this->db->affected_rows();
	}



	function getLatLngFromUserAddress($userAddressId){
		$sql = "SELECT creator_user_id, lat, lng FROM user_addresses WHERE address_id = ?";
		$query = $this->db->query($sql,array($userAddressId));
		return $query->row();
	}
	//
	function getUserAddress($addressId){
		$sql = "SELECT address, reference, lat, lng, FROM user_addresses
		WHERE address_id = ?";
		$query = $this->db->query($addressId);
		return $query->result();
	}

	function createUserAddress($address, $reference, $lat, $lng, $userId){
		$sql = "INSERT INTO  user_addresses(address, reference, lat, lng, reg_date, creator_user_id)
			VALUES (?,?,?,?, NOW(),?)";
		$query = $this->db->query($sql,array($address, $reference, $lat, $lng, $userId));
		return $lastInsertId = $this->db->insert_id();;
	}

	function updateUserAddress($addressId, $address, $reference, $lat, $lng){
		$sql = "UPDATE  user_addresses
		SET address = ?
		reference = ?,
		lat = ?,
		lng = ?,
		reg_date = NOW()
		WHERE address_id = ?";
		$query = $this->db->query(array($address, $reference, $lat, $lng, $addressId));
		return $addressId;
	}
	
	function login($userName){
		$sql="SELECT user_id FROM users WHERE username = ?";
		$query = $this->db->query($sql, array($userName));
		return $query->row();
	}


}