SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


INSERT INTO `users` (`id`, `name`, `password`, `level`, `email`, `expires`, `session_id`, `current_radio`, `browser`, `individual`, `login_datetime`, `login_address`, `client_address`, `user_id`, `updated`) VALUES
(1, 'admin',   '21232f297a57a5a743894a0e4a801fc3', 0, '', NULL, NULL, NULL, '', '', '2017-01-01 00:00:00', '0.0.0.0', '127.0.0.1', 1, NOW()),
(2, 'gateway', 'ad82294638da5cbdaf4930e7acc0324f', 2, '', NULL, NULL, NULL, '', '', '2017-01-01 00:00:00', '0.0.0.0', '127.0.0.1', 1, NOW());

INSERT INTO `allocates` (`group`, `type`, `resource_id`, `user_id`, `client_address`, `updated`) VALUES
(1, 4, 1, 1, '127.0.0.1', NOW()),
(1, 4, 2, 1, '127.0.0.1', NOW());

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
