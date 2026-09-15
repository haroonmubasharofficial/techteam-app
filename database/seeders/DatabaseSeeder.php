<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        DB::table('units')->insertOrIgnore([['name'=>'Unit','symbol'=>'Unit','created_at'=>now(),'updated_at'=>now()],['name'=>'Piece','symbol'=>'Pc','created_at'=>now(),'updated_at'=>now()],['name'=>'Meter','symbol'=>'m','created_at'=>now(),'updated_at'=>now()],['name'=>'Service','symbol'=>'Service','created_at'=>now(),'updated_at'=>now()]]);
        DB::table('product_categories')->insertOrIgnore([['name'=>'Networking','created_at'=>now(),'updated_at'=>now()],['name'=>'CCTV','created_at'=>now(),'updated_at'=>now()],['name'=>'Computer Hardware','created_at'=>now(),'updated_at'=>now()],['name'=>'Services','created_at'=>now(),'updated_at'=>now()]]);
        DB::table('customers')->insertOrIgnore([['company_name'=>'Pak Kuwait Textiles Limited','contact_person'=>'Mr. Muhammad Ali','address'=>'8-M Model Town Extension, Lahore Pakistan','phone'=>'','email'=>'','payment_terms'=>'Credit','created_at'=>now(),'updated_at'=>now()]]);
        DB::table('products')->insertOrIgnore([['name'=>'TP-Link Archer C20 Router','description'=>'New Wi-Fi Router (Model: TP-Link Archer C20)','item_type'=>'product','brand'=>'TP-Link','model'=>'Archer C20','warranty'=>'One year Warranty','purchase_cost'=>7500,'default_selling_price'=>9750,'tax_rate'=>0,'is_active'=>1,'track_serial'=>0,'created_at'=>now(),'updated_at'=>now()]]);
    }
}
