<?php
/******************************************************************************
 * phpGridServer
 *
 * GNU LESSER GENERAL PUBLIC LICENSE
 * Version 2.1, February 1999
 *
 */

require_once("lib/types/Vector3.php");

if(!isset($_RPC_REQUEST->UserID))
{
	http_response_code("400");
	exit;
}

if(!isset($_RPC_REQUEST->Position))
{
	http_response_code("400");
	exit;
}

if(!isset($_RPC_REQUEST->LookAt))
{
	http_response_code("400");
	exit;
}

if(!isset($_RPC_REQUEST->RegionID))
{
	http_response_code("400");
	exit;
}

if(!UUID::IsUUID($_RPC_REQUEST->RegionID))
{
	http_response_code("400");
	exit;
}

if(!Vector3::IsVector3($_RPC_REQUEST->Position))
{
	http_response_code("400");
	exit;
}

if(!Vector3::IsVector3($_RPC_REQUEST->LookAt))
{
	http_response_code("400");
	exit;
}

require_once("lib/services.php");
$gridUserService = getService("RPC_GridUser");
$userAccountService = getService("RPC_UserAccount");

if(UUID::IsUUID($_RPC_REQUEST->UserID))
{
	try
	{
		$userAccountService->getAccountByID(null, $_RPC_REQUEST->UserID);
	}
	catch(Exception $e)
	{
		/* No account, no GridUser entry */
		sendBooleanResponse(False);
		exit;
	}
}
else
{
	try
	{
		$userAccountService->getAccountByID(null, substr($_RPC_REQUEST->UserID, 0, 36));
		$_RPC_REQUEST->UserID = substr($_RPC_REQUEST->UserID, 0, 36);
	}
	catch(Exception $e)
	{
		/* No account, keep it HG */
	}
}

try
{
	$gridUserService->setPosition($_RPC_REQUEST->UserID, $_RPC_REQUEST->RegionID, $_RPC_REQUEST->Position, $_RPC_REQUEST->LookAt);
	sendBooleanResponse(True);
}
catch(Exception $e)
{
	sendBooleanResponse(False);
}
