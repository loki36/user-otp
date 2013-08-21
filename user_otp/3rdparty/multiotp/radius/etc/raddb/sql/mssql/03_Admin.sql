USE [master]
GO

IF EXISTS (SELECT * FROM sys.server_principals WHERE name = N'testsqluser')
DROP LOGIN [testsqluser]
GO

CREATE LOGIN [testsqluser] WITH PASSWORD=N'LondoN2012', DEFAULT_DATABASE=[radius], DEFAULT_LANGUAGE=[us_english], CHECK_EXPIRATION=OFF, CHECK_POLICY=ON
GO

-- Required by Vista when logged in against a domain ? mmmmm

EXEC sys.sp_addsrvrolemember @loginame = N'testsqluser', @rolename = N'sysadmin'

USE [radius]
GO

IF  EXISTS (SELECT * FROM sys.database_principals WHERE name = N'testsqluser')
DROP USER [testsqluser]
GO

CREATE USER [testsqluser] FOR LOGIN [testsqluser] WITH DEFAULT_SCHEMA = radius
GO

EXEC sp_addrolemember N'db_datareader', N'testsqluser'
EXEC sp_addrolemember N'db_datawriter', N'testsqluser'
GO

GRANT SELECT ON radcheck TO testsqluser
GRANT SELECT ON radreply TO testsqluser
GRANT SELECT ON radgroupcheck TO testsqluser
GRANT SELECT ON radgroupreply TO testsqluser
GRANT SELECT ON radusergroup TO testsqluser
GO

GRANT ALL on radacct TO testsqluser
GRANT ALL on radpostauth TO testsqluser
GO