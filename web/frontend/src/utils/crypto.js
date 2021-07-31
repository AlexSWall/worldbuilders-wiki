export async function sha256hex(strIn)
{
	// Encode input string as UTF-8
	const bytes = new TextEncoder().encode(strIn);                    

	// SHA-256 hash
	const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);

	// Convert ArrayBuffer to array of bytes
	const hashArray = Array.from(new Uint8Array(hashBuffer));

	// Convert bytes to hex string                  
	const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

	return hashHex;
}
