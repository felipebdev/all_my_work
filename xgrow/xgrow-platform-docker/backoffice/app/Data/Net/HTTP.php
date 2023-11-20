<?php

namespace App\Data\Net;

use App\Data\Object\DynamicObject;
/**
 * Class HTTP
 *
 * @class HTTP
 * @package App\Data\Net
 *
 * @property bool $isGET
 * @property bool $isPOST
 * @property bool $isPUT
 * @property bool $isDELETE
 * @property bool $isPATCH
 * @property bool $isHEAD
 * @property bool $isOPTIONS
 */
class HTTP extends DynamicObject
{
	/**
	 * This means that the server has received the request headers, and that the client should proceed to send the request body (in the case of a request for which a body needs to be sent; for example, a POST request). If the request body is large, sending it to a server when a request has already been rejected based upon inappropriate headers is inefficient. To have a server check if the request could be accepted based on the request's headers alone, a client must send Expect: 100-continue as a header in its initial request and check if a 100 Continue status code is received in response before continuing (or receive 417 Expectation Failed and not continue).
	 * @const {number}
	 *
	 * @param array|null $data
	 * @param string $statusMessage
	 */
	public const CONTINUE = 100;
	/**
	 * This means the requester has asked the server to switch protocols and the server is acknowledging that it will do so.
	 * @const {number}
	 */
	public const SWITCHING_PROTOCOLS = 101;
	/**
	 * As a WebDAV request may contain many sub-requests involving file operations, it may take a long time to complete the request. This code indicates that the server has received and is processing the request, but no response is available yet. This prevents the client from timing out and assuming the request was lost.
	 * @const {number}
	 */
	public const PROCESSING = 102;
	/**
	 * Standard response for successful HTTP requests. The actual response will depend on the request method used. In a GET request, the response will contain an entity corresponding to the requested resource. In a POST request the response will contain an entity describing or containing the result of the action.
	 * General status code. Most common code used to indicate success.
	 * @const {number}
	 */
	public const OK = 200;
	/**
	 * The request has been fulfilled and resulted in a new resource being created.
	 * Successful creation occurred (via either POST or PUT). Set the Location header to contain a link to the newly-created resource (on POST). Response body content may or may not be present.
	 * @const {number}
	 */
	public const CREATED = 201;
	/**
	 * The request has been accepted for processing, but the processing has not been completed. The request might or might not eventually be acted upon, as it might be disallowed when processing actually takes place.
	 * @const {number}
	 */
	public const ACCEPTED = 202;
	/**
	 * The server successfully processed the request, but is returning information that may be from another source.
	 * Not present in HTTP/1.0: available since HTTP/1.1
	 * @const {number}
	 */
	public const NON_AUTHORITATIVE_INFORMATION = 203;
	/**
	 * The server successfully processed the request, but is not returning any content.
	 * Status when wrapped responses (e.g. JSEND) are not used and nothing is in the body (e.g. DELETE).
	 * @const {number}
	 */
	public const NO_CONTENT = 204;
	/**
	 * The server successfully processed the request, but is not returning any content. Unlike a 204 response, this response requires that the requester reset the document view.
	 * @const {number}
	 */
	public const RESET_CONTENT = 205;
	/**
	 * The server is delivering only part of the resource due to a range header sent by the client. The range header is used by tools like wget to enable resuming of interrupted downloads, or split a download into multiple simultaneous streams.
	 * @const {number}
	 */
	public const PARTIAL_CONTENT = 206;
	/**
	 * The message body that follows is an XML message and can contain a number of separate response codes, depending on how many sub-requests were made.
	 * @const {number}
	 */
	public const MULTI_STATUS = 207;
	/**
	 * The members of a DAV binding have already been enumerated in a previous reply to this request, and are not being included again.
	 * @const {number}
	 */
	public const ALREADY_REPORTED = 208;
	/**
	 * The server has fulfilled a GET request for the resource, and the response is a representation of the result of one or more instance-manipulations applied to the current instance.
	 * @const {number}
	 */
	public const IM_USED = 226;
	/**
	 * Indicates multiple options for the resource that the client may follow. It, for instance, could be used to present different format options for video, list files with different extensions, or word sense disambiguation.
	 * @const {number}
	 */
	public const MULTIPLE_CHOICES = 300;
	/**
	 * This and all future requests should be directed to the given URI.
	 * @const {number}
	 */
	public const MOVED_PERMANENTLY = 301;
	/**
	 * This is an example of industry practice contradicting the standard.[2] The HTTP/1.0 specification (RFC 1945) required the client to perform a temporary redirect (the original describing phrase was "Moved Temporarily"), but popular browsers implemented 302 with the functionality of a 303 See Other. Therefore, HTTP/1.1 added status codes 303 and 307 to distinguish between the two behaviours. However, some Web applications and frameworks use the 302 status code as if it were the 303.
	 * @const {number}
	 */
	public const FOUND = 302;
	/**
	 * The response to the request can be found under another URI using a GET method. When received in response to a POST (or PUT/DELETE), it should be assumed that the server has received the data and the redirect should be issued with a separate GET message.
	 * Since HTTP/1.1
	 * @const {number}
	 */
	public const SEE_OTHER = 303;
	/**
	 * Indicates the resource has not been modified since last requested. Typically, the HTTP client provides a header like the If-Modified-Since header to provide a time against which to compare. Using this saves bandwidth and reprocessing on both the server and client, as only the header data must be sent and received in comparison to the entirety of the page being re-processed by the server, then sent again using more bandwidth of the server and client.
	 * Used for conditional GET calls to reduce band-width usage. If used, must set the Date, Content-Location, ETag headers to what they would have been on a regular GET call. There must be no body on the response.
	 * @const {number}
	 */
	public const NOT_MODIFIED = 304;
	/**
	 * Many HTTP clients (such as Mozilla and Internet Explorer) do not correctly handle responses with this status code, primarily for security reasons.
	 * @const {number}
	 */
	public const USE_PROXY = 305;
	/**
	 * No longer used. Originally meant "Subsequent requests should use the specified proxy."
	 * @const {number}
	 */
	public const UNUSED = 306;
	/**
	 * In this case, the request should be repeated with another URI; however, future requests can still use the original URI. In contrast to 302, the request method should not be changed when reissuing the original request. For instance, a POST request must be repeated using another POST request.
	 * @const {number}
	 */
	public const TEMPORARY_REDIRECT = 307;
	/**
	 * The request, and all future requests should be repeated using another URI. 307 and 308 (as proposed) parallel the behaviours of 302 and 301, but do not require the HTTP method to change. So, for example, submitting a form to a permanently redirected resource may continue smoothly.
	 * @const {number}
	 */
	public const PERMANENT_REDIRECT = 308;
	/**
	 * The request cannot be fulfilled due to bad syntax.
	 * General error when fulfilling the request would cause an invalid state. Domain validation errors, missing data, etc. are some examples.
	 * @const {number}
	 */
	public const BAD_REQUEST = 400;
	/**
	 * Similar to 403 Forbidden, but specifically for use when authentication is possible but has failed or not yet been provided. The response must include a WWW-Authenticate header field containing a challenge applicable to the requested resource. See Basic access authentication and Digest access authentication.
	 * Error code response for missing or invalid authentication token.
	 * @const {number}
	 */
	public const UNAUTHORIZED = 401;
	/**
	 * Reserved for future use. The original intention was that this code might be used as part of some form of digital cash or micropayment scheme, but that has not happened, and this code is not usually used. As an example of its use, however, Apple's MobileMe service generates a 402 error ("httpStatusCode:402" in the Mac OS X Console log) if the MobileMe account is delinquent.
	 * @const {number}
	 */
	public const PAYMENT_REQUIRED = 402;
	/**
	 * The request was a legal request, but the server is refusing to respond to it. Unlike a 401 Unauthorized response, authenticating will make no difference.
	 * Error code for user not authorized to perform the operation or the resource is unavailable for some reason (e.g. time constraints, etc.).
	 * @const {number}
	 */
	public const FORBIDDEN = 403;
	/**
	 * The requested resource could not be found but may be available again in the future. Subsequent requests by the client are permissible.
	 * Used when the requested resource is not found, whether it doesn't exist or if there was a 401 or 403 that, for security reasons, the service wants to mask.
	 * @const {number}
	 */
	public const NOT_FOUND = 404;
	/**
	 * A request was made of a resource using a request method not supported by that resource; for example, using GET on a form which requires data to be presented via POST, or using PUT on a read-only resource.
	 * @const {number}
	 */
	public const METHOD_NOT_ALLOWED = 405;
	/**
	 * The requested resource is only capable of generating content not acceptable according to the Accept headers sent in the request.
	 * @const {number}
	 */
	public const NOT_ACCEPTABLE = 406;
	/**
	 * The client must first authenticate itself with the proxy.
	 * @const {number}
	 */
	public const PROXY_AUTHENTICATION_REQUIRED = 407;
	/**
	 * The server timed out waiting for the request. According to W3 HTTP specifications: "The client did not produce a request within the time that the server was prepared to wait. The client MAY repeat the request without modifications at any later time."
	 * @const {number}
	 */
	public const REQUEST_TIMEOUT = 408;
	/**
	 * Indicates that the request could not be processed because of conflict in the request, such as an edit conflict.
	 * Whenever a resource conflict would be caused by fulfilling the request. Duplicate entries and deleting root objects when cascade-delete is not supported are a couple of examples.
	 * @const {number}
	 */
	public const CONFLICT = 409;
	/**
	 * Indicates that the resource requested is no longer available and will not be available again. This should be used when a resource has been intentionally removed and the resource should be purged. Upon receiving a 410 status code, the client should not request the resource again in the future. Clients such as search engines should remove the resource from their indices. Most use cases do not require clients and search engines to purge the resource, and a "404 Not Found" may be used instead.
	 * @const {number}
	 */
	public const GONE = 410;
	/**
	 * The request did not specify the length of its content, which is required by the requested resource.
	 * @const {number}
	 */
	public const LENGTH_REQUIRED = 411;
	/**
	 * The server does not meet one of the preconditions that the requester put on the request.
	 * @const {number}
	 */
	public const PRECONDITION_FAILED = 412;
	/**
	 * The request is larger than the server is willing or able to process.
	 * @const {number}
	 */
	public const REQUEST_ENTITY_TOO_LARGE = 413;
	/**
	 * The URI provided was too long for the server to process.
	 * @const {number}
	 */
	public const REQUEST_URI_TOO_LONG = 414;
	/**
	 * The request entity has a media type which the server or resource does not support. For example, the client uploads an image as image/svg+xml, but the server requires that images use a different format.
	 * @const {number}
	 */
	public const UNSUPPORTED_MEDIA_TYPE = 415;
	/**
	 * The client has asked for a portion of the file, but the server cannot supply that portion. For example, if the client asked for a part of the file that lies beyond the end of the file.
	 * @const {number}
	 */
	public const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	/**
	 * The server cannot meet the requirements of the Expect request-header field.
	 * @const {number}
	 */
	public const EXPECTATION_FAILED = 417;
	/**
	 * This code was defined in 1998 as one of the traditional IETF April Fools' jokes, in RFC 2324, Hyper Text Coffee Pot Control Protocol, and is not expected to be implemented by actual HTTP servers. However, known implementations do exist. An Nginx HTTP server uses this code to simulate goto-like behaviour in its configuration.
	 * @const {number}
	 */
	public const IM_A_TEAPOT = 418;
	/**
	 * Returned by the Twitter Search and Trends API when the client is being rate limited. Likely a reference to this number's association with marijuana. Other services may wish to implement the 429 Too Many HTTPs response code instead.
	 * @const {number}
	 */
	public const ENHANCE_YOUR_CALM = 420;
	/**
	 * The request was well-formed but was unable to be followed due to semantic errors.
	 * @const {number}
	 */
	public const UNPROCESSABLE_ENTITY = 422;
	/**
	 * The resource that is being accessed is locked.
	 * @const {number}
	 */
	public const LOCKED = 423;
	/**
	 * The request failed due to failure of a previous request (e.g. a PROPPATCH).
	 * @const {number}
	 */
	public const FAILED_DEPENDENCY = 424;
	/**
	 * Defined in drafts of "WebDAV Advanced Collections Protocol", but not present in "Web Distributed Authoring and Versioning (WebDAV) Ordered Collections Protocol".
	 * @const {number}
	 */
	public const RESERVED_FOR_WEBDAV = 425;
	/**
	 * The client should switch to a different protocol such as TLS/1.0.
	 * @const {number}
	 */
	public const UPGRADE_REQUIRED = 426;
	/**
	 * The origin server requires the request to be conditional. Intended to prevent "the "lost update" problem, where a client GETs a resource's state, modifies it, and PUTs it back to the server, when meanwhile a third party has modified the state on the server, leading to a conflict.
	 * @const {number}
	 */
	public const PRECONDITION_REQUIRED = 428;
	/**
	 * The user has sent too many requests in a given amount of time. Intended for use with rate limiting schemes.
	 * @const {number}
	 */
	public const TOO_MANY_REQUESTS = 429;
	/**
	 * The server is unwilling to process the request because either an individual header field, or all the header fields collectively, are too large.
	 * @const {number}
	 */
	public const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	/**
	 * An Nginx HTTP server extension. The server returns no information to the client and closes the connection (useful as a deterrent for malware).
	 * @const {number}
	 */
	public const NO_RESPONSE = 444;
	/**
	 * A Microsoft extension. The request should be retried after performing the appropriate action.
	 * @const {number}
	 */
	public const RETRY_WITH = 449;
	/**
	 * A Microsoft extension. This error is given when Windows Parental Controls are turned on and are blocking access to the given webpage.
	 * @const {number}
	 */
	public const BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
	/**
	 * Intended to be used when resource access is denied for legal reasons, e.g. censorship or government-mandated blocked access. A reference to the 1953 dystopian novel Fahrenheit 451, where books are outlawed, and the autoignition temperature of paper, 451°F.
	 * @const {number}
	 */
	public const UNAVAILABLE_FOR_LEGAL_REASONS = 451;
	/**
	 * An Nginx HTTP server extension. This code is introduced to log the case when the connection is closed by client while HTTP server is processing its request, making server unable to send the HTTP header back.
	 * @const {number}
	 */
	public const CLIENT_CLOSED_REQUEST = 499;
	/**
	 * A generic error message, given when no more specific message is suitable.
	 * The general catch-all error when the server-side throws an exception.
	 * @const {number}
	 */
	public const INTERNAL_SERVER_ERROR = 500;
	/**
	 * The server either does not recognise the request method, or it lacks the ability to fulfill the request.
	 * @const {number}
	 */
	public const NOT_IMPLEMENTED = 501;
	/**
	 * The server was acting as a gateway or proxy and received an invalid response from the upstream server.
	 * @const {number}
	 */
	public const BAD_GATEWAY = 502;
	/**
	 * The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a temporary state.
	 * @const {number}
	 */
	public const SERVICE_UNAVAILABLE = 503;
	/**
	 * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.
	 * @const {number}
	 */
	public const GATEWAY_TIMEOUT = 504;
	/**
	 * The server does not support the HTTP protocol version used in the request.
	 * @const {number}
	 */
	public const HTTP_VERSION_NOT_SUPPORTED = 505;
	/**
	 * Transparent content negotiation for the request results in a circular reference.
	 * @const {number}
	 */
	public const VARIANT_ALSO_NEGOTIATES = 506;
	/**
	 * The server is unable to store the representation needed to complete the request.
	 * @const {number}
	 */
	public const INSUFFICIENT_STORAGE = 507;
	/**
	 * The server detected an infinite loop while processing the request (sent in lieu of 208).
	 * @const {number}
	 */
	public const LOOP_DETECTED = 508;
	/**
	 * This status code, while used by many servers, is not specified in any RFCs.
	 * @const {number}
	 */
	public const BANDWIDTH_LIMIT_EXCEEDED = 509;
	/**
	 * Further extensions to the request are required for the server to fulfill it.
	 * @const {number}
	 */
	public const NOT_EXTENDED = 510;
	/**
	 * The client needs to authenticate to gain network access. Intended for use by intercepting proxies used to control access to the network (e.g., "captive portals" used to require agreement to Terms of Service before granting full Internet access via a Wi-Fi hotspot).
	 * @const {number}
	 */
	public const NETWORK_AUTHENTICATION_REQUIRED = 511;
	/**
	 *This status code is not specified in any RFCs, but is used by some HTTP proxies to signal a network read timeout behind the proxy to a client in front of the proxy.
	 * @const {number}
	 */
	public const NETWORK_READ_TIMEOUT_ERROR = 598;
	/**
	 * This status code is not specified in any RFCs, but is used by some HTTP proxies to signal a network connect timeout behind the proxy to a client in front of the proxy.
	 * @const {number}
	 */
	public const NETWORK_CONNECT_TIMEOUT_ERROR = 599;
	/**
	 * @var array
	 */
	public const STATUS_TEXT =
	[
		/**
		 * This means that the server has received the request headers, and that the client should proceed to send the request body (in the case of a request for which a body needs to be sent; for example, a POST request). If the request body is large, sending it to a server when a request has already been rejected based upon inappropriate headers is inefficient. To have a server check if the request could be accepted based on the request's headers alone, a client must send Expect: 100-continue as a header in its initial request and check if a 100 Continue status code is received in response before continuing (or receive 417 Expectation Failed and not continue).
		 * @const {number}
		 */
		100 => 'Continue',
		/**
		 * This means the requester has asked the server to switch protocols and the server is acknowledging that it will do so.
		 * @const {number}
		 */
		101 => 'Switching Protocols',
		/**
		 * As a WebDAV request may contain many sub-requests involving file operations, it may take a long time to complete the request. This code indicates that the server has received and is processing the request, but no response is available yet. This prevents the client from timing out and assuming the request was lost.
		 * @const {number}
		 */
		102 => 'Processing',
		/**
		 * Standard response for successful HTTP requests. The actual response will depend on the request method used. In a GET request, the response will contain an entity corresponding to the requested resource. In a POST request the response will contain an entity describing or containing the result of the action.
		 * General status code. Most common code used to indicate success.
		 * @const {number}
		 */
		200 => 'Ok',
		/**
		 * The request has been fulfilled and resulted in a new resource being created.
		 * Successful creation occurred (via either POST or PUT). Set the Location header to contain a link to the newly-created resource (on POST). Response body content may or may not be present.
		 * @const {number}
		 */
		201 => 'Created',
		/**
		 * The request has been accepted for processing, but the processing has not been completed. The request might or might not eventually be acted upon, as it might be disallowed when processing actually takes place.
		 * @const {number}
		 */
		202 => 'Accepted',
		/**
		 * The server successfully processed the request, but is returning information that may be from another source.
		 * Not present in HTTP/1.0: available since HTTP/1.1
		 * @const {number}
		 */
		203 => 'Non Authoritative Information',
		/**
		 * The server successfully processed the request, but is not returning any content.
		 * Status when wrapped responses (e.g. JSEND) are not used and nothing is in the body (e.g. DELETE).
		 * @const {number}
		 */
		204 => 'No Content',
		/**
		 * The server successfully processed the request, but is not returning any content. Unlike a 204 response, this response requires that the requester reset the document view.
		 * @const {number}
		 */
		205 => 'Reset Content',
		/**
		 * The server is delivering only part of the resource due to a range header sent by the client. The range header is used by tools like wget to enable resuming of interrupted downloads, or split a download into multiple simultaneous streams.
		 * @const {number}
		 */
		206 => 'Partial Content',
		/**
		 * The message body that follows is an XML message and can contain a number of separate response codes, depending on how many sub-requests were made.
		 * @const {number}
		 */
		207 => 'Multi Status',
		/**
		 * The members of a DAV binding have already been enumerated in a previous reply to this request, and are not being included again.
		 * @const {number}
		 */
		208 => 'Already Reported',
		/**
		 * The server has fulfilled a GET request for the resource, and the response is a representation of the result of one or more instance-manipulations applied to the current instance.
		 * @const {number}
		 */
		226 => 'Im Used',
		/**
		 * Indicates multiple options for the resource that the client may follow. It, for instance, could be used to present different format options for video, list files with different extensions, or word sense disambiguation.
		 * @const {number}
		 */
		300 => 'Multiple Choices',
		/**
		 * This and all future requests should be directed to the given URI.
		 * @const {number}
		 */
		301 => 'Moved Permanently',
		/**
		 * This is an example of industry practice contradicting the standard.[2] The HTTP/1.0 specification (RFC 1945) required the client to perform a temporary redirect (the original describing phrase was "Moved Temporarily"), but popular browsers implemented 302 with the functionality of a 303 See Other. Therefore, HTTP/1.1 added status codes 303 and 307 to distinguish between the two behaviours. However, some Web applications and frameworks use the 302 status code as if it were the 303.
		 * @const {number}
		 */
		302 => 'Found',
		/**
		 * The response to the request can be found under another URI using a GET method. When received in response to a POST (or PUT/DELETE), it should be assumed that the server has received the data and the redirect should be issued with a separate GET message.
		 * Since HTTP/1.1
		 * @const {number}
		 */
		303 => 'See Other',
		/**
		 * Indicates the resource has not been modified since last requested. Typically, the HTTP client provides a header like the If-Modified-Since header to provide a time against which to compare. Using this saves bandwidth and reprocessing on both the server and client, as only the header data must be sent and received in comparison to the entirety of the page being re-processed by the server, then sent again using more bandwidth of the server and client.
		 * Used for conditional GET calls to reduce band-width usage. If used, must set the Date, Content-Location, ETag headers to what they would have been on a regular GET call. There must be no body on the response.
		 * @const {number}
		 */
		304 => 'Not Modified',
		/**
		 * Many HTTP clients (such as Mozilla and Internet Explorer) do not correctly handle responses with this status code, primarily for security reasons.
		 * @const {number}
		 */
		305 => 'Use Proxy',
		/**
		 * No longer used. Originally meant "Subsequent requests should use the specified proxy."
		 * @const {number}
		 */
		306 => 'Unused',
		/**
		 * In this case, the request should be repeated with another URI; however, future requests can still use the original URI. In contrast to 302, the request method should not be changed when reissuing the original request. For instance, a POST request must be repeated using another POST request.
		 * @const {number}
		 */
		307 => 'Temporary Redirect',
		/**
		 * The request, and all future requests should be repeated using another URI. 307 and 308 (as proposed) parallel the behaviours of 302 and 301, but do not require the HTTP method to change. So, for example, submitting a form to a permanently redirected resource may continue smoothly.
		 * @const {number}
		 */
		308 => 'Permanent Redirect',
		/**
		 * The request cannot be fulfilled due to bad syntax.
		 * General error when fulfilling the request would cause an invalid state. Domain validation errors, missing data, etc. are some examples.
		 * @const {number}
		 */
		400 => 'Bad Request',
		/**
		 * Similar to 403 Forbidden, but specifically for use when authentication is possible but has failed or not yet been provided. The response must include a WWW-Authenticate header field containing a challenge applicable to the requested resource. See Basic access authentication and Digest access authentication.
		 * Error code response for missing or invalid authentication token.
		 * @const {number}
		 */
		401 => 'Unauthorized',
		/**
		 * Reserved for future use. The original intention was that this code might be used as part of some form of digital cash or micropayment scheme, but that has not happened, and this code is not usually used. As an example of its use, however, Apple's MobileMe service generates a 402 error ("httpStatusCode:402" in the Mac OS X Console log) if the MobileMe account is delinquent.
		 * @const {number}
		 */
		402 => 'Payment Required',
		/**
		 * The request was a legal request, but the server is refusing to respond to it. Unlike a 401 Unauthorized response, authenticating will make no difference.
		 * Error code for user not authorized to perform the operation or the resource is unavailable for some reason (e.g. time constraints, etc.).
		 * @const {number}
		 */
		403 => 'Forbidden',
		/**
		 * The requested resource could not be found but may be available again in the future. Subsequent requests by the client are permissible.
		 * Used when the requested resource is not found, whether it doesn't exist or if there was a 401 or 403 that, for security reasons, the service wants to mask.
		 * @const {number}
		 */
		404 => 'Not Found',
		/**
		 * A request was made of a resource using a request method not supported by that resource; for example, using GET on a form which requires data to be presented via POST, or using PUT on a read-only resource.
		 * @const {number}
		 */
		405 => 'Method Not Allowed',
		/**
		 * The requested resource is only capable of generating content not acceptable according to the Accept headers sent in the request.
		 * @const {number}
		 */
		406 => 'Not Acceptable',
		/**
		 * The client must first authenticate itself with the proxy.
		 * @const {number}
		 */
		407 => 'Proxy Authentication Required',
		/**
		 * The server timed out waiting for the request. According to W3 HTTP specifications: "The client did not produce a request within the time that the server was prepared to wait. The client MAY repeat the request without modifications at any later time."
		 * @const {number}
		 */
		408 => 'Request Timeout',
		/**
		 * Indicates that the request could not be processed because of conflict in the request, such as an edit conflict.
		 * Whenever a resource conflict would be caused by fulfilling the request. Duplicate entries and deleting root objects when cascade-delete is not supported are a couple of examples.
		 * @const {number}
		 */
		409 => 'Conflict',
		/**
		 * Indicates that the resource requested is no longer available and will not be available again. This should be used when a resource has been intentionally removed and the resource should be purged. Upon receiving a 410 status code, the client should not request the resource again in the future. Clients such as search engines should remove the resource from their indices. Most use cases do not require clients and search engines to purge the resource, and a "404 Not Found" may be used instead.
		 * @const {number}
		 */
		410 => 'Gone',
		/**
		 * The request did not specify the length of its content, which is required by the requested resource.
		 * @const {number}
		 */
		411 => 'Length Required',
		/**
		 * The server does not meet one of the preconditions that the requester put on the request.
		 * @const {number}
		 */
		412 => 'Precondition Failed',
		/**
		 * The request is larger than the server is willing or able to process.
		 * @const {number}
		 */
		413 => 'Request Entity Too Large',
		/**
		 * The URI provided was too long for the server to process.
		 * @const {number}
		 */
		414 => 'Request Uri Too Long',
		/**
		 * The request entity has a media type which the server or resource does not support. For example, the client uploads an image as image/svg+xml, but the server requires that images use a different format.
		 * @const {number}
		 */
		415 => 'Unsupported Media Type',
		/**
		 * The client has asked for a portion of the file, but the server cannot supply that portion. For example, if the client asked for a part of the file that lies beyond the end of the file.
		 * @const {number}
		 */
		416 => 'Requested Range Not Satisfiable',
		/**
		 * The server cannot meet the requirements of the Expect request-header field.
		 * @const {number}
		 */
		417 => 'Expectation Failed',
		/**
		 * This code was defined in 1998 as one of the traditional IETF April Fools' jokes, in RFC 2324, Hyper Text Coffee Pot Control Protocol, and is not expected to be implemented by actual HTTP servers. However, known implementations do exist. An Nginx HTTP server uses this code to simulate goto-like behaviour in its configuration.
		 * @const {number}
		 */
		418 => 'Im A Teapot',
		/**
		 * Returned by the Twitter Search and Trends API when the client is being rate limited. Likely a reference to this number's association with marijuana. Other services may wish to implement the 429 Too Many HTTPs response code instead.
		 * @const {number}
		 */
		420 => 'Enhance Your Calm',
		/**
		 * The request was well-formed but was unable to be followed due to semantic errors.
		 * @const {number}
		 */
		422 => 'Unprocessable Entity',
		/**
		 * The resource that is being accessed is locked.
		 * @const {number}
		 */
		423 => 'Locked',
		/**
		 * The request failed due to failure of a previous request (e.g. a PROPPATCH).
		 * @const {number}
		 */
		424 => 'Failed Dependency',
		/**
		 * Defined in drafts of "WebDAV Advanced Collections Protocol", but not present in "Web Distributed Authoring and Versioning (WebDAV) Ordered Collections Protocol".
		 * @const {number}
		 */
		425 => 'Reserved For Webdav',
		/**
		 * The client should switch to a different protocol such as TLS/1.0.
		 * @const {number}
		 */
		426 => 'Upgrade Required',
		/**
		 * The origin server requires the request to be conditional. Intended to prevent "the "lost update" problem, where a client GETs a resource's state, modifies it, and PUTs it back to the server, when meanwhile a third party has modified the state on the server, leading to a conflict.
		 * @const {number}
		 */
		428 => 'Precondition Required',
		/**
		 * The user has sent too many requests in a given amount of time. Intended for use with rate limiting schemes.
		 * @const {number}
		 */
		429 => 'Too Many Requests',
		/**
		 * The server is unwilling to process the request because either an individual header field, or all the header fields collectively, are too large.
		 * @const {number}
		 */
		431 => 'Request Header Fields Too Large',
		/**
		 * An Nginx HTTP server extension. The server returns no information to the client and closes the connection (useful as a deterrent for malware).
		 * @const {number}
		 */
		444 => 'No Response',
		/**
		 * A Microsoft extension. The request should be retried after performing the appropriate action.
		 * @const {number}
		 */
		449 => 'Retry With',
		/**
		 * A Microsoft extension. This error is given when Windows Parental Controls are turned on and are blocking access to the given webpage.
		 * @const {number}
		 */
		450 => 'Blocked By Windows Parental Controls',
		/**
		 * Intended to be used when resource access is denied for legal reasons, e.g. censorship or government-mandated blocked access. A reference to the 1953 dystopian novel Fahrenheit 451, where books are outlawed, and the autoignition temperature of paper, 451°F.
		 * @const {number}
		 */
		451 => 'Unavailable For Legal Reasons',
		/**
		 * An Nginx HTTP server extension. This code is introduced to log the case when the connection is closed by client while HTTP server is processing its request, making server unable to send the HTTP header back.
		 * @const {number}
		 */
		499 => 'Client Closed Request',
		/**
		 * A generic error message, given when no more specific message is suitable.
		 * The general catch-all error when the server-side throws an exception.
		 * @const {number}
		 */
		500 => 'Internal Server Error',
		/**
		 * The server either does not recognise the request method, or it lacks the ability to fulfill the request.
		 * @const {number}
		 */
		501 => 'Not Implemented',
		/**
		 * The server was acting as a gateway or proxy and received an invalid response from the upstream server.
		 * @const {number}
		 */
		502 => 'Bad Gateway',
		/**
		 * The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a temporary state.
		 * @const {number}
		 */
		503 => 'Service Unavailable',
		/**
		 * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.
		 * @const {number}
		 */
		504 => 'Gateway Timeout',
		/**
		 * The server does not support the HTTP protocol version used in the request.
		 * @const {number}
		 */
		505 => 'Http Version Not Supported',
		/**
		 * Transparent content negotiation for the request results in a circular reference.
		 * @const {number}
		 */
		506 => 'Variant Also Negotiates',
		/**
		 * The server is unable to store the representation needed to complete the request.
		 * @const {number}
		 */
		507 => 'Insufficient Storage',
		/**
		 * The server detected an infinite loop while processing the request (sent in lieu of 208).
		 * @const {number}
		 */
		508 => 'Loop Detected',
		/**
		 * This status code, while used by many servers, is not specified in any RFCs.
		 * @const {number}
		 */
		509 => 'Bandwidth Limit Exceeded',
		/**
		 * Further extensions to the request are required for the server to fulfill it.
		 * @const {number}
		 */
		510 => 'Not Extended',
		/**
		 * The client needs to authenticate to gain network access. Intended for use by intercepting proxies used to control access to the network (e.g., "captive portals" used to require agreement to Terms of Service before granting full Internet access via a Wi-Fi hotspot).
		 * @const {number}
		 */
		511 => 'Network Authentication Required',
		/**
		 *This status code is not specified in any RFCs, but is used by some HTTP proxies to signal a network read timeout behind the proxy to a client in front of the proxy.
		 * @const {number}
		 */
		598 => 'Network Read Timeout Error',
		/**
		 * This status code is not specified in any RFCs, but is used by some HTTP proxies to signal a network connect timeout behind the proxy to a client in front of the proxy.
		 * @const {number}
		 */
		599 => 'Network Connect Timeout Error'
	];
	/**
	 * @const string
	 */
	public const GET = 'GET';
	/**
	 * @const string
	 */
	public const POST = 'POST';
	/**
	 * @const string
	 */
	public const PUT = 'PUT';
	/**
	 * @const string
	 */
	public const PATCH = 'PATCH';
	/**
	 * @const string
	 */
	public const OPTIONS = 'OPTIONS';
	/**
	 * @const string
	 */
	public const DELETE = 'DELETE';
	/**
	 * @const string
	 */
	public const HEAD = 'HEAD';
	/**
	 * @var string
	 */
	private ?string $_method = null;
	/**
	 * @constructor
	 */
	public function __construct()
	{
		$this->_method = $_SERVER['REQUEST_METHOD'];
	}
	/**
	 * @return bool
	 */
	public function isIsGET(): bool
	{
		return $this->_method === self::GET;
	}

	/**
	 * @return bool
	 */
	public function isIsPOST(): bool
	{
		return $this->_method === self::POST;
	}

	/**
	 * @return bool
	 */
	public function isIsPUT(): bool
	{
		return $this->_method === self::PUT;
	}

	/**
	 * @return bool
	 */
	public function isIsDELETE(): bool
	{
		return $this->_method === self::DELETE;
	}

	/**
	 * @return bool
	 */
	public function isIsPATCH(): bool
	{
		return $this->_method === self::PATCH;
	}

	/**
	 * @return bool
	 */
	public function isIsHEAD(): bool
	{
		return $this->_method === self::HEAD;
	}

	/**
	 * @return bool
	 */
	public function isIsOPTIONS(): bool
	{
		return $this->_method === self::OPTIONS;
	}
}
