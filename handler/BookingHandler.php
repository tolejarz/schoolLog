<?php
class BookingHandler extends Handler {
    public function doList() {
        $booking_controller = new ReservationController($this->dbo);
        $booking_controller->doList();
    }
    
    public function doAdd() {
        $booking_controller = new ReservationController($this->dbo);
        $booking_controller->doAdd();
    }
}
?>
