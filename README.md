# avisos
Plugin Avisos para GLPI.

El plugin avisos le permite agregar advertencias sobre tipos de glpi: tickets, computadoras, usuarios ...

Mediante una simple consulta en base de datos podemos avisar al usuario por ejemplo de si un ticket no tiene técnico asignado.

----------------------------------------
select 'x' from glpi_tickets
where not exists
(select '' from glpi_tickets_users
where tickets_id=glpi_tickets.id
and type = 2)
and glpi_tickets.entities_id=1
and glpi_tickets.status < 6

-----------------------------------------

Puedes configurar: 

Si quieres que muestre el aviso al crear el objeto y al mostrarlo.

Color de fuente en la descripción del aviso.

Color y tamaño de fuente en la cabecera del aviso.

Permisos de ejecución del aviso por perfiles de usuario o por grupos de usuarios.
