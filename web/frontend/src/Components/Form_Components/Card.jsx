import React from 'react';

export default function Card({ title, children })
{
	return (
		<div className='card'>
			<div className='card-header'>
				{ title }
			</div>
			<div className='card-body'>
				{ children }
			</div>
		</div>
	);
}
