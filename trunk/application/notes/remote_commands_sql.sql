SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS remote_commands (
  remote_command_key bigint(20) NOT NULL AUTO_INCREMENT,
  remote_command_is_repeat tinyint(1) NOT NULL DEFAULT '0',
  remote_command_remote_id varchar(30) DEFAULT NULL,
  remote_command_signal_id varchar(255) DEFAULT NULL,
  remote_command_time_sent bigint(20) NOT NULL,
  remote_command_processed tinyint(1) NOT NULL DEFAULT '0',
  remote_command_inserted_time bigint(20) DEFAULT NULL,
  remote_command_host_dongle varchar(50) DEFAULT NULL,
  PRIMARY KEY (remote_command_key)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13324 ;
