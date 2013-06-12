SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

INSERT INTO `urlandrss` (`url`, `rss_url`, `blogname`) VALUES
('http://www.mediabistro.com/alltwitter/', 'http://www.mediabistro.com/alltwitter/feed', 'All Twitter'),
('http://www.geeky-gadgets.com/', 'http://feeds.feedburner.com/geeky-gadgets', 'Geeky-Gadgets'),
('http://www.huffingtonpost.com/?country=US', 'http://feeds.huffingtonpost.com/huffingtonpost/LatestNews', 'huffingtonpost US'),
('http://techcrunch.com/', 'http://feedproxy.google.com/TechCrunch', 'TECHCRUNCH'),
('http://9to5mac.com/', 'http://feeds.feedburner.com/9To5Mac-MacAllDay', '9to5');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
