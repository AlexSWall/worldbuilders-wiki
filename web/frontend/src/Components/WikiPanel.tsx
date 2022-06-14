import React from 'react';

export default function WikiPanel({ title, html })
{
	return (
		<>
			<h1 className="wiki-header">{ title }</h1>
			<div dangerouslySetInnerHTML={ { __html: html } } />
		</>
	);
}
