-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2025 at 09:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `curtaindb`
--
CREATE DATABASE IF NOT EXISTS `curtaindb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `curtaindb`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `AddCurtain`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddCurtain` (IN `p_id` INT, IN `p_Type` VARCHAR(50), IN `p_Color` VARCHAR(50), IN `p_Material` VARCHAR(50), IN `p_Dimensions` VARCHAR(100), IN `p_Price` BIGINT)   BEGIN
    INSERT INTO curtain (CurtainID, Type, Color, Material, Dimensions, Price) VALUES (p_id, p_Type, p_Color, p_Material, p_Dimensions, p_Price);
END$$

DROP PROCEDURE IF EXISTS `AddEmployee`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddEmployee` (IN `p_id` INT, IN `p_Role` VARCHAR(50), IN `p_Salary` BIGINT)   BEGIN
    INSERT INTO employee (EmployeeID, Role, Salary) VALUES (p_id, p_Role, p_Salary);
END$$

DROP PROCEDURE IF EXISTS `AddOrder`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddOrder` (IN `p_id` INT, IN `p_CustomerID` INT, IN `p_EmployeeID` INT, IN `p_PeymentID` INT, IN `p_OrderDate` DATE, IN `p_Status` VARCHAR(50), IN `p_curtainsJSON` TEXT)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE len INT;
    DECLARE cur_id INT;


    SET p_curtainsJSON = JSON_UNQUOTE(JSON_EXTRACT(p_curtainsJSON, '$'));
    SET len = JSON_LENGTH(p_curtainsJSON);


    INSERT INTO `order` (OrderID, CustomerID, EmployeeID, PeymentID, OrderDate, Status) VALUES (p_id, p_CustomerID, p_EmployeeID, p_PeymentID, p_OrderDate, p_Status);

    SET len = JSON_LENGTH(p_curtainsJSON);
    
    WHILE i < len DO
        SET cur_id = JSON_UNQUOTE(JSON_EXTRACT(p_curtainsJSON, CONCAT('$[', i, ']')));
        SET cur_id = CAST(cur_id AS SIGNED);
        IF cur_id > 0 AND EXISTS (SELECT 1 FROM curtain WHERE CurtainID = cur_id) THEN
            INSERT IGNORE INTO order_curtain (OrderID, CurtainID) VALUES (p_id, cur_id);
        END IF;
        SET i = i + 1;
    END WHILE;
END$$

DROP PROCEDURE IF EXISTS `AddPayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddPayment` (IN `p_id` INT, IN `p_Type` VARCHAR(45), IN `p_Amount` BIGINT, IN `p_Description` VARCHAR(250))   BEGIN
    INSERT INTO peyment (PeymentID, Type, Amount, Description) VALUES (p_id, p_Type, p_Amount, p_Description);
END$$

DROP PROCEDURE IF EXISTS `add_customer`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_customer` (IN `p_id` INT, IN `p_Address` VARCHAR(255), IN `p_JoinDate` DATE)   BEGIN
    INSERT INTO customer (CustomerID, Address, JoinDate) VALUES (p_id, p_Address, p_JoinDate);
END$$

DROP PROCEDURE IF EXISTS `add_person`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_person` (IN `p_id` INT, IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_PhoneNumber` VARCHAR(15), IN `p_Email` VARCHAR(100))   BEGIN
    INSERT INTO person (PersonID, FirstName, LastName, PhoneNumber, Email) VALUES (p_id, p_FirstName, p_LastName, p_PhoneNumber, p_Email);
END$$

DROP PROCEDURE IF EXISTS `DeleteCurtain`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteCurtain` (IN `p_id` INT)   BEGIN
    DELETE FROM curtain WHERE CurtainID = p_id;
END$$

DROP PROCEDURE IF EXISTS `DeleteEmployee`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteEmployee` (IN `p_id` INT)   BEGIN
    DELETE FROM employee WHERE EmployeeID = p_id;
END$$

DROP PROCEDURE IF EXISTS `DeleteOrder`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteOrder` (IN `p_id` INT)   BEGIN
    DELETE FROM `order` WHERE OrderID = p_id;
END$$

DROP PROCEDURE IF EXISTS `DeletePayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeletePayment` (IN `p_id` INT)   BEGIN
    DELETE FROM peyment WHERE PeymentID = p_id;
END$$

DROP PROCEDURE IF EXISTS `delete_customer`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_customer` (IN `p_id` INT)   BEGIN
    DELETE FROM customer WHERE CustomerID = p_id;
END$$

DROP PROCEDURE IF EXISTS `delete_person`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_person` (IN `p_id` INT)   BEGIN
    DELETE FROM person WHERE PersonID = p_id;
END$$

DROP PROCEDURE IF EXISTS `EditCurtain`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `EditCurtain` (IN `p_id` INT, IN `p_Type` VARCHAR(50), IN `p_Color` VARCHAR(50), IN `p_Material` VARCHAR(50), IN `p_Dimensions` VARCHAR(100), IN `p_Price` BIGINT)   BEGIN
    UPDATE curtain SET Type = p_Type, Color = p_Color, Material = p_Material, Dimensions = p_Dimensions, Price = p_Price WHERE CurtainID = p_id;
END$$

DROP PROCEDURE IF EXISTS `EditEmployee`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `EditEmployee` (IN `p_id` INT, IN `p_Role` VARCHAR(50), IN `p_Salary` BIGINT)   BEGIN
    UPDATE employee SET Role = p_Role, Salary = p_Salary WHERE EmployeeID = p_id;
END$$

DROP PROCEDURE IF EXISTS `EditOrder`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `EditOrder` (IN `p_id` INT, IN `p_CustomerID` INT, IN `p_EmployeeID` INT, IN `p_PeymentID` INT, IN `p_OrderDate` DATE, IN `p_Status` VARCHAR(50), IN `p_curtainsJSON` TEXT)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE len INT;
    DECLARE cur_id INT;

    SET p_curtainsJSON = JSON_UNQUOTE(JSON_EXTRACT(p_curtainsJSON, '$'));
    SET len = JSON_LENGTH(p_curtainsJSON);


    UPDATE `order` SET CustomerID = p_CustomerID, EmployeeID = p_EmployeeID, PeymentID = p_PeymentID, OrderDate = p_OrderDate, Status = p_Status WHERE OrderID = p_id;
    
    DELETE FROM order_curtain WHERE OrderID = p_id;

    SET len = JSON_LENGTH(p_curtainsJSON);
    
    WHILE i < len DO
        SET cur_id = JSON_UNQUOTE(JSON_EXTRACT(p_curtainsJSON, CONCAT('$[', i, ']')));
        SET cur_id = CAST(cur_id AS SIGNED);
        IF cur_id > 0 AND EXISTS (SELECT 1 FROM curtain WHERE CurtainID = cur_id) THEN
            INSERT IGNORE INTO order_curtain (OrderID, CurtainID) VALUES (p_id, cur_id);
        END IF;
        SET i = i + 1;
    END WHILE;
END$$

DROP PROCEDURE IF EXISTS `EditPayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `EditPayment` (IN `p_id` INT, IN `p_Type` VARCHAR(45), IN `p_Amount` BIGINT, IN `p_Description` VARCHAR(250))   BEGIN
    UPDATE peyment SET Type = p_Type, Amount = p_Amount, Description = p_Description WHERE PeymentID = p_id;
END$$

DROP PROCEDURE IF EXISTS `edit_customer`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `edit_customer` (IN `p_id` INT, IN `p_Address` VARCHAR(255), IN `p_JoinDate` DATE)   BEGIN
    UPDATE customer SET Address = p_Address, JoinDate = p_JoinDate WHERE CustomerID = p_id;
END$$

DROP PROCEDURE IF EXISTS `edit_person`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `edit_person` (IN `p_id` INT, IN `p_FirstName` VARCHAR(100), IN `p_LastName` VARCHAR(100), IN `p_PhoneNumber` VARCHAR(15), IN `p_Email` VARCHAR(100))   BEGIN
    UPDATE person SET FirstName = p_FirstName, LastName = p_LastName, PhoneNumber = p_PhoneNumber, Email = p_Email WHERE PersonID = p_id;
END$$

DROP PROCEDURE IF EXISTS `GetCurtains`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCurtains` ()   BEGIN
    SELECT * FROM curtain;
END$$

DROP PROCEDURE IF EXISTS `GetCurtains4`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCurtains4` ()   BEGIN
    SELECT CurtainID, Type, Color, Price FROM curtain;
END$$

DROP PROCEDURE IF EXISTS `GetCustomerInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCustomerInfo` ()   BEGIN
    SELECT c.CustomerID, p.FirstName, p.LastName, c.Address, c.JoinDate 
    FROM customer c 
    JOIN person p ON c.CustomerID = p.PersonID;
END$$

DROP PROCEDURE IF EXISTS `GetCustomers4`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCustomers4` ()   BEGIN
    SELECT CustomerID, Address, JoinDate FROM customer;
END$$

DROP PROCEDURE IF EXISTS `GetEmployees2`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetEmployees2` ()   BEGIN
    SELECT e.EmployeeID, p.FirstName, p.LastName, e.Role, e.Salary 
    FROM employee e 
    JOIN person p ON e.EmployeeID = p.PersonID;
END$$

DROP PROCEDURE IF EXISTS `GetEmployees4`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetEmployees4` ()   BEGIN
    SELECT EmployeeID, Role, Salary FROM employee;
END$$

DROP PROCEDURE IF EXISTS `GetOrders`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetOrders` ()   BEGIN
    SELECT o.OrderID, o.CustomerID, o.EmployeeID, o.PeymentID, o.OrderDate, o.Status,
    GROUP_CONCAT(CONCAT(c.Type, ' - ', c.Color) SEPARATOR ', ') AS Curtains
    FROM `order` o
    LEFT JOIN order_curtain oc ON o.OrderID = oc.OrderID
    LEFT JOIN curtain c ON oc.CurtainID = c.CurtainID
    GROUP BY o.OrderID;
END$$

DROP PROCEDURE IF EXISTS `GetPersonInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPersonInfo` ()   BEGIN
    SELECT PersonID, FirstName, LastName, PhoneNumber, Email FROM person;
END$$

DROP PROCEDURE IF EXISTS `GetPersons`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPersons` ()   BEGIN
    SELECT * FROM person;
END$$

DROP PROCEDURE IF EXISTS `GetPersons2`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPersons2` ()   BEGIN
    SELECT PersonID, FirstName, LastName, PhoneNumber, Email FROM person;
END$$

DROP PROCEDURE IF EXISTS `GetPeyments`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPeyments` ()   BEGIN
    SELECT * FROM peyment;
END$$

DROP PROCEDURE IF EXISTS `GetPeyments4`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPeyments4` ()   BEGIN
    SELECT PeymentID, Type, Amount, Description FROM peyment;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `curtain`
--

DROP TABLE IF EXISTS `curtain`;
CREATE TABLE `curtain` (
  `CurtainID` int(11) NOT NULL,
  `Type` varchar(50) NOT NULL,
  `Color` varchar(50) NOT NULL,
  `Material` varchar(50) NOT NULL,
  `Dimensions` varchar(100) NOT NULL,
  `Price` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curtain`
--

INSERT DELAYED IGNORE INTO `curtain` (`CurtainID`, `Type`, `Color`, `Material`, `Dimensions`, `Price`) VALUES
(12, 'کرکره ای', 'قرمز', 'یشمی', '4 در 3', 240),
(58, 'پارچه ای', 'سیاه', 'کنافی', '4 در 2', 120),
(78, 'زبرا', 'سبز', 'کاغذی', '4 در 5', 200);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `CustomerID` int(11) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `JoinDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT DELAYED IGNORE INTO `customer` (`CustomerID`, `Address`, `JoinDate`) VALUES
(1234567890, 'قاین میدان شیرازی', '2025-12-12');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
  `EmployeeID` int(11) NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Salary` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT DELAYED IGNORE INTO `employee` (`EmployeeID`, `Role`, `Salary`) VALUES
(1111111111, 'کارمند اداری', 22);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `OrderID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `PeymentID` int(11) NOT NULL,
  `OrderDate` date NOT NULL,
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_curtain`
--

DROP TABLE IF EXISTS `order_curtain`;
CREATE TABLE `order_curtain` (
  `OrderID` int(11) NOT NULL,
  `CurtainID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `PersonID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `PhoneNumber` varchar(15) NOT NULL,
  `Email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `person`
--

INSERT DELAYED IGNORE INTO `person` (`PersonID`, `FirstName`, `LastName`, `PhoneNumber`, `Email`) VALUES
(1111111111, 'محمد', 'محمدی', '09123412342', 'mo@gmail.com'),
(1234567890, 'ابراهیم', 'ابراهیمی', '09523456789', 'ds@gmail.com'),
(2147483647, 'رضا', 'رضایی', '09987654321', 'rz@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `peyment`
--

DROP TABLE IF EXISTS `peyment`;
CREATE TABLE `peyment` (
  `PeymentID` int(11) NOT NULL,
  `Type` varchar(45) NOT NULL,
  `Amount` bigint(20) NOT NULL,
  `Description` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peyment`
--

INSERT DELAYED IGNORE INTO `peyment` (`PeymentID`, `Type`, `Amount`, `Description`) VALUES
(2802, 'حضوری', 1600, 'حضوری'),
(7800, 'اینترنتی', 2500, 'اینترنتی');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `curtain`
--
ALTER TABLE `curtain`
  ADD PRIMARY KEY (`CurtainID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EmployeeID`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`OrderID`),
  ADD UNIQUE KEY `PeymentID_UNIQUE` (`PeymentID`),
  ADD KEY `employee order_idx` (`EmployeeID`),
  ADD KEY `orders_ibfk_1` (`CustomerID`),
  ADD KEY `peyment order_idx` (`PeymentID`);

--
-- Indexes for table `order_curtain`
--
ALTER TABLE `order_curtain`
  ADD PRIMARY KEY (`OrderID`,`CurtainID`),
  ADD KEY `CURTAIN_idx` (`CurtainID`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`PersonID`);

--
-- Indexes for table `peyment`
--
ALTER TABLE `peyment`
  ADD PRIMARY KEY (`PeymentID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `person` (`PersonID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `person` (`PersonID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `employee order` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peyment order` FOREIGN KEY (`PeymentID`) REFERENCES `peyment` (`PeymentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_curtain`
--
ALTER TABLE `order_curtain`
  ADD CONSTRAINT `CURTAIN` FOREIGN KEY (`CurtainID`) REFERENCES `curtain` (`CurtainID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ORDER` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE;


--
-- Metadata
--
USE `phpmyadmin`;

--
-- Metadata for table curtain
--

--
-- Metadata for table customer
--

--
-- Metadata for table employee
--

--
-- Metadata for table order
--

--
-- Metadata for table order_curtain
--

--
-- Metadata for table person
--

--
-- Metadata for table peyment
--

--
-- Metadata for database curtaindb
--
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
