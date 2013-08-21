USE [radius]
GO

CREATE TABLE [radacct] (
	[RadAcctId] [numeric](21, 0) IDENTITY (1, 1) NOT NULL PRIMARY KEY,
	[AcctSessionId] [varchar] (64) DEFAULT (''),
	[AcctUniqueId] [varchar] (32) DEFAULT (''),
	[UserName] [varchar] (64) DEFAULT (''),
	[GroupName] [varchar] (64) DEFAULT (''),
	[Realm] [varchar] (64) DEFAULT (''),
	[NASIPAddress] [varchar] (15) DEFAULT (''),
	[NASPortId] [varchar] (15) NULL ,
	[NASPortType] [varchar] (32) NULL ,
	[AcctStartTime] [datetime] NOT NULL ,
	[AcctStopTime] [datetime] NOT NULL ,
	[AcctSessionTime] [bigint] NULL ,
	[AcctAuthentic] [varchar] (32) NULL ,
	[ConnectInfo_start] [varchar] (32) DEFAULT NULL,
	[ConnectInfo_stop] [varchar] (32) DEFAULT NULL,
	[AcctInputOctets] [bigint] NULL ,
	[AcctOutputOctets] [bigint] NULL ,
	[CalledStationId] [varchar] (30) DEFAULT (''),
	[CallingStationId] [varchar] (30) DEFAULT (''),
	[AcctTerminateCause] [varchar] (32) DEFAULT (''),
	[ServiceType] [varchar] (32) NULL ,
	[FramedProtocol] [varchar] (32) NULL ,
	[FramedIPAddress] [varchar] (15) DEFAULT (''),
	[XAscendSessionSvrKey] [varchar] (10) DEFAULT NULL,
	[AcctStartDelay] [int] NULL ,
	[AcctStopDelay] [int] NULL
) ON [PRIMARY]
GO


CREATE TABLE [radcheck] (
	[Id] [int] IDENTITY (1, 1) NOT NULL PRIMARY KEY,
	[UserName] [varchar] (64) NOT NULL ,
	[Attribute] [varchar] (32) NOT NULL ,
	[Value] [varchar] (253) NOT NULL ,
	[op] [char] (2) NULL
) ON [PRIMARY]
GO

CREATE TABLE [radgroupcheck] (
	[Id] [int] IDENTITY (1, 1) NOT NULL PRIMARY KEY,
	[GroupName] [varchar] (64) NOT NULL ,
	[Attribute] [varchar] (32) NOT NULL ,
	[Value] [varchar] (253) NOT NULL ,
	[op] [char] (2) NULL
) ON [PRIMARY]
GO


CREATE TABLE [radgroupreply] (
	[Id] [int] IDENTITY (1, 1) NOT NULL PRIMARY KEY,
	[GroupName] [varchar] (64) NOT NULL ,
	[Attribute] [varchar] (32) NOT NULL ,
	[Value] [varchar] (253) NOT NULL ,
	[op] [char] (2) NULL ,
	[prio] [int] NOT NULL
) ON [PRIMARY]
GO

CREATE TABLE [radreply] (
	[Id] [int] IDENTITY (1, 1) NOT NULL PRIMARY KEY,
	[UserName] [varchar] (64) NOT NULL ,
	[Attribute] [varchar] (32) NOT NULL ,
	[Value] [varchar] (253) NOT NULL ,
	[op] [char] (2) NULL
) ON [PRIMARY]
GO

CREATE TABLE [radusergroup] (
	[Id] [int] IDENTITY (1, 1) NOT NULL PRIMARY KEY,
	[UserName] [varchar] (64) NOT NULL ,
	[GroupName] [varchar] (64) NULL
) ON [PRIMARY]
GO


CREATE TABLE [radpostauth] (
        [Id] [int] IDENTITY (1, 1) NOT NULL PRIMARY KEY,
        [userName] [varchar] (64) NOT NULL ,
        [pass] [varchar] (64) NOT NULL ,
        [reply] [varchar] (32) NOT NULL ,
        [authdate] [datetime] NOT NULL
)
GO

CREATE TABLE [nas] (
  [Id] [int] IDENTITY (1, 1) NOT NULL PRIMARY KEY,
  nasname [varchar](128) NOT NULL,
  shortname [varchar](32),
  [type] [varchar](30) DEFAULT 'other',
  [ports] [int],
  [secret] [varchar](60) DEFAULT 'secret' NOT NULL,
  [server] [varchar](64),
  community [varchar](50),
  [description] [varchar](200) DEFAULT 'RADIUS Client'
)
GO