import React from 'react';

export default function SelectOption({ value, text })
{
	return (
		<option value={ value }>
			{ text }
		</option>
	);
}
