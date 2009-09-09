delimiter //
create trigger uwsservice_new after insert on uwsservice
for each row
	begin
		call update_balance(NEW.contributor, NEW.lifetime*NEW.factor);
	end//
delimiter ;

delimiter //
create trigger uwsinventorize_new after insert on uwsinventorize
for each row
	begin
		call update_inventory(NEW.uwsunit, NEW.amount*NEW.factor);
	end//
delimiter ;



delimiter $$
create procedure update_balance(who varchar(255), amount double)
	begin
		update uwscontributors set balance = balance + amount where contributor=who;
	end$$
delimiter ;

delimiter $$
create procedure update_inventory(which varchar(255), amount double)
	begin
		update uwsunits set inventory = inventory + amount where unit=which;
	end$$
delimiter ;

