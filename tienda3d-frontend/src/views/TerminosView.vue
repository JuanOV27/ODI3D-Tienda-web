<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="max-w-3xl mx-auto px-4 py-10">
    <RouterLink to="/registro" class="text-odi-amarillo text-sm hover:underline">&larr; Volver al registro</RouterLink>

    <header class="mt-6 mb-10 text-center">
      <p class="text-odi-muted text-xs uppercase tracking-widest mb-2">ODI3D — Ortiz Diseño e Impresión 3D</p>
      <h1 class="text-3xl font-display font-bold text-odi-texto mb-2">Términos y Condiciones de Uso del Software</h1>
      <p class="text-odi-muted text-sm">Plataforma Web de Gestión y Tienda en Línea</p>
      <div class="text-odi-muted text-xs mt-4 space-y-0.5">
        <p>Versión: 1.0 &nbsp;·&nbsp; Fecha de emisión: Junio 2026</p>
        <p>Titular: Juan David Ortiz Vera</p>
        <p>Ubicación: Aguachica, Cesar, Colombia</p>
      </div>
    </header>

    <article class="space-y-10 text-odi-muted text-sm leading-relaxed">

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">1. Identificación del Titular y del Servicio</h2>
        <p>ODI3D — Ortiz Diseño e Impresión 3D es un microemprendimiento dedicado a la manufactura aditiva (impresión 3D), cuyo titular es Juan David Ortiz Vera, persona natural, con domicilio en el municipio de Aguachica, departamento de Cesar, República de Colombia.</p>
        <p class="mt-3">El presente documento regula el acceso y uso del software desarrollado para ODI3D, compuesto por dos módulos principales: (i) el sistema de gestión interna denominado <em>gestion3d</em>, de uso exclusivo del personal autorizado, y (ii) la tienda en línea pública denominada <em>tienda3d</em>, accesible a clientes registrados. Ambos módulos comparten la misma base de datos y están diseñados como un sistema integral de gestión y comercialización de servicios de impresión 3D.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">2. Definiciones</h2>
        <p class="mb-3">Para efectos del presente documento, los siguientes términos tendrán el significado que se indica a continuación:</p>
        <ul class="space-y-2 list-disc list-outside pl-5">
          <li><span class="text-odi-texto font-medium">Términos y Condiciones (T&amp;C):</span> El presente documento contractual de adhesión que regula el uso de la Plataforma.</li>
          <li><span class="text-odi-texto font-medium">Plataforma:</span> El conjunto de aplicaciones web que conforman el sistema ODI3D (gestion3d y tienda3d).</li>
          <li><span class="text-odi-texto font-medium">Titular:</span> Juan David Ortiz Vera, propietario de ODI3D y desarrollador del software.</li>
          <li><span class="text-odi-texto font-medium">Usuario:</span> Toda persona natural o jurídica que acceda a la Plataforma en cualquiera de sus roles.</li>
          <li><span class="text-odi-texto font-medium">Cliente:</span> Usuario registrado en tienda3d que solicita cotizaciones, realiza pedidos o hace uso del catálogo.</li>
          <li><span class="text-odi-texto font-medium">Empleado:</span> Colaborador de ODI3D con acceso autorizado al módulo interno de gestion3d.</li>
          <li><span class="text-odi-texto font-medium">Administrador (Admin):</span> Usuario con acceso total a la Plataforma, incluyendo configuración del sistema y datos financieros.</li>
          <li><span class="text-odi-texto font-medium">Solicitud / Cotización:</span> Requerimiento enviado por un Cliente para obtener una estimación del costo de un servicio de impresión 3D.</li>
          <li><span class="text-odi-texto font-medium">Archivo 3D:</span> Archivo digital en formato STL, OBJ, 3MF u otros formatos compatibles, cargado por el Usuario para solicitar un servicio.</li>
          <li><span class="text-odi-texto font-medium">Dato Personal:</span> Cualquier información vinculada o que pueda asociarse a una persona natural determinada o determinable, según la Ley 1581 de 2012.</li>
          <li><span class="text-odi-texto font-medium">Habeas Data:</span> Derecho fundamental consagrado en el Artículo 15 de la Constitución Política de Colombia y desarrollado en la Ley 1581 de 2012.</li>
          <li><span class="text-odi-texto font-medium">Mensaje de datos:</span> Toda información generada, enviada, recibida, almacenada o comunicada por medios electrónicos, según la Ley 527 de 1999.</li>
          <li><span class="text-odi-texto font-medium">Propiedad Intelectual:</span> Derechos de autor, marcas, patentes y demás derechos reconocidos por la Ley 23 de 1982 y la Decisión 351 de 1993 de la CAN.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">3. Aceptación de los Términos y Condiciones</h2>
        <p class="mb-3">El acceso, registro o uso de la Plataforma implica la aceptación plena, expresa e informada de los presentes Términos y Condiciones. Esta aceptación se formaliza mediante:</p>
        <ul class="space-y-2 list-disc list-outside pl-5">
          <li>El acto de marcar la casilla de aceptación (&#x201C;He leído y acepto los Términos y Condiciones&#x201D;) durante el proceso de registro.</li>
          <li>El inicio de sesión por parte de usuarios registrados con anterioridad a la publicación de la presente versión, quienes serán notificados y deberán aceptar los nuevos términos para continuar operando.</li>
          <li>La utilización continuada de la Plataforma tras la publicación de modificaciones debidamente notificadas.</li>
        </ul>
        <p class="mt-3">Si el Usuario no acepta total o parcialmente estos Términos, deberá abstenerse de usar la Plataforma. De conformidad con el Artículo 45 de la Ley 1480 de 2011 (Estatuto del Consumidor), el Usuario tiene derecho a recibir información veraz, suficiente y oportuna antes de contratar.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">4. Descripción del Servicio</h2>
        <p class="mb-3">La Plataforma ODI3D presta los siguientes servicios a través de sus módulos:</p>

        <h3 class="text-odi-texto font-medium mb-2">4.1 Módulo tienda3d (público)</h3>
        <ul class="space-y-2 list-disc list-outside pl-5 mb-4">
          <li>Catálogo en línea de productos impresos en 3D disponibles para pedido.</li>
          <li>Sistema de solicitudes de proyectos personalizados: el Cliente describe su necesidad, adjunta imágenes, archivos 3D y especificaciones.</li>
          <li>Seguimiento del estado de sus solicitudes y pedidos.</li>
          <li>Sistema de reseñas sobre productos y servicios.</li>
          <li>Canal de chat interno entre el Cliente y el personal de ODI3D.</li>
          <li>Redirección a WhatsApp Business para la confirmación y cierre comercial de pedidos.</li>
        </ul>

        <h3 class="text-odi-texto font-medium mb-2">4.2 Módulo gestion3d (interno)</h3>
        <ul class="space-y-2 list-disc list-outside pl-5">
          <li>Gestión de cotizaciones, pedidos y trazabilidad de producción.</li>
          <li>Inventario de filamento y suministros.</li>
          <li>Gestión de máquinas y depreciación.</li>
          <li>Panel financiero y de rentabilidad, de acceso exclusivo del Administrador.</li>
        </ul>

        <p class="mt-3">El Titular se reserva el derecho de activar, desactivar o modificar módulos de la Plataforma mediante un sistema de banderas de funcionalidad (<em>feature flags</em>), sin que ello constituya incumplimiento del presente contrato, siempre que se notifique al Usuario con una anticipación razonable.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">5. Registro de Usuarios y Obligaciones</h2>
        <p class="mb-3">Para acceder a los servicios personalizados de la Plataforma, el Usuario deberá completar el proceso de registro proporcionando información verdadera, exacta, actualizada y completa. El registro genera las siguientes obligaciones:</p>
        <ul class="space-y-2 list-disc list-outside pl-5">
          <li>Mantener la confidencialidad de sus credenciales de acceso (usuario y contraseña).</li>
          <li>Notificar inmediatamente al Titular ante cualquier uso no autorizado de su cuenta o vulneración de seguridad.</li>
          <li>No ceder, vender ni transferir su cuenta a terceros.</li>
          <li>No crear cuentas falsas, múltiples o con identidad ajena.</li>
          <li>Actualizar sus datos personales cuando estos cambien.</li>
        </ul>
        <p class="mt-3">El Titular podrá suspender o cancelar una cuenta cuando existan indicios razonables de uso fraudulento, actividad ilícita o violación de los presentes Términos. Las contraseñas se almacenan mediante el algoritmo bcrypt con factor de costo 12; el Titular no tiene acceso a las contraseñas en texto plano.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">6. Propiedad Intelectual</h2>
        <h3 class="text-odi-texto font-medium mb-2">6.1 Derechos del Titular</h3>
        <p class="mb-4">El código fuente, el diseño, la arquitectura, los algoritmos de cotización, la marca ODI3D, el isologo (incluyendo la tortuga Morrocoy y los elementos hexagonales), el nombre comercial, los manuales y la documentación asociada son propiedad exclusiva de Juan David Ortiz Vera, protegidos por la Ley 23 de 1982, la Ley 44 de 1993 y la Decisión 351 de 1993 de la Comunidad Andina de Naciones. El Usuario no adquiere ningún derecho de propiedad intelectual sobre la Plataforma por el simple hecho de usarla. Queda expresamente prohibida la copia, reproducción, modificación, distribución, ingeniería inversa o cualquier explotación comercial de la Plataforma sin autorización escrita del Titular.</p>

        <h3 class="text-odi-texto font-medium mb-2">6.2 Archivos 3D cargados por el Usuario</h3>
        <p class="mb-3">Cuando el Cliente carga un archivo 3D en la Plataforma para solicitar un servicio, declara y garantiza que es el titular de los derechos de propiedad intelectual sobre dicho archivo, o que cuenta con autorización expresa del titular para utilizarlo con fines de impresión 3D; que el archivo no reproduce diseños, personajes, marcas, patentes ni cualquier otro elemento protegido por derechos de terceros sin la correspondiente licencia; y que el archivo no corresponde a ninguna de las categorías de impresión prohibida descritas en la Sección 9 del presente documento.</p>
        <p>El Titular no realiza verificación automática de la titularidad sobre los archivos 3D. La responsabilidad por violación de derechos de propiedad intelectual de terceros recae exclusivamente sobre el Usuario que cargó el archivo. En caso de reclamación de terceros, el Usuario deberá indemnizar y mantener indemne a ODI3D. Los archivos 3D cargados se almacenan únicamente para la prestación del servicio solicitado y no serán reproducidos, comercializados ni cedidos a terceros sin consentimiento del Usuario.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">7. Tratamiento de Datos Personales</h2>
        <p class="mb-4">En cumplimiento del Artículo 15 de la Constitución Política de Colombia, la Ley 1581 de 2012, el Decreto 1377 de 2013 y el Decreto Único Reglamentario 1074 de 2015, ODI3D informa su política de tratamiento de datos personales.</p>

        <h3 class="text-odi-texto font-medium mb-2">7.1 Responsable del tratamiento</h3>
        <p class="mb-4">Juan David Ortiz Vera, ODI3D, con domicilio en Aguachica, Cesar, Colombia. El Usuario puede ejercer sus derechos a través del canal de WhatsApp Business o del correo electrónico registrado en la Plataforma.</p>

        <h3 class="text-odi-texto font-medium mb-2">7.2 Datos recopilados</h3>
        <p class="mb-4">La Plataforma recopila nombre completo, correo electrónico y número de contacto al momento del registro; historial de solicitudes, cotizaciones y pedidos; archivos 3D e imágenes cargadas para solicitudes; reseñas y calificaciones publicadas; y datos de acceso como dirección IP para efectos de seguridad del sistema.</p>

        <h3 class="text-odi-texto font-medium mb-2">7.3 Finalidades del tratamiento</h3>
        <p class="mb-4">Los datos personales recopilados se utilizan exclusivamente para: gestionar solicitudes, cotizaciones y producción; comunicar al Usuario sobre el estado de sus pedidos; mejorar el servicio y la atención al cliente; cumplir obligaciones legales y contables; y garantizar la seguridad e integridad del sistema.</p>

        <h3 class="text-odi-texto font-medium mb-2">7.4 Derechos del titular del dato</h3>
        <p class="mb-4">De conformidad con el Artículo 8 de la Ley 1581 de 2012, el Usuario tiene derecho a conocer, actualizar y rectificar sus datos personales; solicitar prueba de la autorización otorgada; ser informado sobre el uso de sus datos; revocar la autorización y solicitar la supresión de sus datos salvo que exista un deber legal de conservarlos; y presentar quejas ante la Superintendencia de Industria y Comercio (SIC) por infracciones a la normativa de protección de datos.</p>

        <h3 class="text-odi-texto font-medium mb-2">7.5 Medidas de seguridad</h3>
        <p class="mb-4">ODI3D implementa medidas técnicas y organizativas para proteger los datos personales, entre ellas: cifrado de contraseñas con bcrypt, validación de archivos por inspección de bytes mágicos, protección CSRF en formularios, limitación de intentos de acceso y separación de datos financieros del acceso público.</p>

        <h3 class="text-odi-texto font-medium mb-2">7.6 Transferencia a terceros</h3>
        <p>Los datos personales no serán vendidos, cedidos ni transferidos a terceros con fines comerciales. Podrán compartirse únicamente cuando sea requerido por autoridad judicial o administrativa competente, o cuando sea estrictamente necesario para la prestación del servicio, como ocurre al redirigir al Usuario a WhatsApp Business para confirmar pedidos.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">8. Cotizaciones, Pedidos y Proceso Comercial</h2>
        <p class="mb-3">El proceso comercial en la Plataforma se rige por las siguientes condiciones, en concordancia con la Ley 1480 de 2011 (Estatuto del Consumidor).</p>
        <p class="mb-3">Las cotizaciones generadas a través de tienda3d constituyen una oferta estimativa sujeta a confirmación por parte de ODI3D. El precio final podrá variar en función de las características definitivas del archivo 3D validado para producción. El Cliente recibe únicamente el precio final y la factura; los costos internos de producción como consumo de filamento, depreciación de máquinas y márgenes son información confidencial de ODI3D y no se exponen al Cliente.</p>
        <p class="mb-3">La aceptación formal del pedido y la negociación de condiciones de pago y entrega se realizan a través del canal de WhatsApp Business de ODI3D, al cual la Plataforma redirige al Cliente mediante un enlace directo. El contrato de compraventa se perfecciona en dicho canal, no dentro de la Plataforma. ODI3D no gestiona pagos en línea directamente en la versión actual del sistema.</p>
        <p>El Cliente tiene derecho a la garantía mínima legal establecida en el Estatuto del Consumidor para los bienes adquiridos. En caso de desistimiento o cancelación, aplican las condiciones pactadas en el canal de WhatsApp Business al momento de confirmar el pedido.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">9. Conductas Prohibidas y Uso Aceptable</h2>
        <p class="mb-4">Queda expresamente prohibido el uso de la Plataforma para las siguientes actividades, sin perjuicio de las sanciones penales, civiles y administrativas previstas en la legislación colombiana.</p>

        <h3 class="text-odi-texto font-medium mb-2">9.1 En materia de propiedad intelectual y archivos</h3>
        <ul class="space-y-2 list-disc list-outside pl-5 mb-4">
          <li>Cargar archivos 3D que reproduzcan diseños, marcas, personajes u obras protegidas por derechos de autor sin la licencia correspondiente.</li>
          <li>Solicitar la reproducción de réplicas de armas, piezas de armas, o cualquier objeto cuya fabricación, tenencia o distribución esté prohibida o restringida por la legislación colombiana.</li>
          <li>Solicitar la impresión de objetos destinados a dañar personas, animales o bienes ajenos.</li>
        </ul>

        <h3 class="text-odi-texto font-medium mb-2">9.2 En materia de seguridad informática</h3>
        <p class="mb-3">Las siguientes conductas son constitutivas de delito informático según la Ley 1273 de 2009 y serán objeto de denuncia ante las autoridades competentes:</p>
        <ul class="space-y-2 list-disc list-outside pl-5 mb-4">
          <li>Intentar acceder sin autorización a cuentas, módulos o datos de otros usuarios o del sistema interno.</li>
          <li>Realizar ataques de fuerza bruta, inyección de código malicioso (SQL injection, XSS u otros) o cualquier intento de vulnerar la seguridad de la Plataforma.</li>
          <li>Interceptar o alterar comunicaciones o datos transmitidos a través de la Plataforma.</li>
          <li>Cargar archivos que contengan código malicioso, virus, malware o cualquier elemento dañino.</li>
        </ul>

        <h3 class="text-odi-texto font-medium mb-2">9.3 En materia de información y uso</h3>
        <ul class="space-y-2 list-disc list-outside pl-5">
          <li>Registrarse con información falsa o suplantar la identidad de otra persona o entidad.</li>
          <li>Publicar reseñas falsas, difamatorias o que violen el derecho a la honra de terceros (Artículo 21, Constitución Política).</li>
          <li>Usar la Plataforma para actividades de spam, publicidad no autorizada o extracción automatizada de datos.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">10. Reseñas y Contenido Generado por el Usuario</h2>
        <p>El sistema permite a los Clientes publicar reseñas sobre productos y servicios. Al publicar contenido, el Usuario otorga a ODI3D una licencia no exclusiva, gratuita y limitada para mostrar dicho contenido en la Plataforma con fines informativos. Las reseñas deben ser veraces, basadas en experiencias reales y redactadas con respeto. ODI3D se reserva el derecho de moderar o eliminar contenido que viole estos Términos, sea ofensivo, discriminatorio o afecte derechos de terceros. La libertad de expresión garantizada por el Artículo 20 de la Constitución Política no ampara expresiones que constituyan injuria, calumnia o discriminación.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">11. Limitación de Responsabilidad</h2>
        <p class="mb-3">ODI3D realiza sus mejores esfuerzos para garantizar la disponibilidad y funcionamiento de la Plataforma, pero no puede garantizar su operación ininterrumpida. El Titular no será responsable por interrupciones del servicio derivadas de causas de fuerza mayor o mantenimiento programado; por daños derivados del uso indebido de la Plataforma por parte del Usuario; por pérdida de datos imputable al Usuario como la pérdida de credenciales o uso en dispositivos no seguros; por el contenido de sitios o servicios externos a los que la Plataforma redirija; ni por reclamaciones de terceros por violación de propiedad intelectual derivadas de archivos 3D cargados por el Usuario.</p>
        <p>En ningún caso la responsabilidad total de ODI3D frente a un Usuario podrá superar el valor del servicio contratado que dio origen al daño reclamado.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">12. Disponibilidad del Servicio y Modificaciones</h2>
        <p>ODI3D se reserva el derecho de modificar, suspender o descontinuar, temporal o permanentemente, la Plataforma o cualquiera de sus funcionalidades. Las modificaciones a los presentes Términos serán notificadas a los Usuarios registrados mediante aviso visible en la Plataforma al iniciar sesión y notificación al correo electrónico registrado, con un plazo mínimo de quince (15) días hábiles de antelación para cambios sustanciales. El uso continuado de la Plataforma después del período de notificación constituirá aceptación de los nuevos Términos.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">13. Seguridad del Sistema</h2>
        <p class="mb-3">La Plataforma implementa las siguientes medidas de seguridad: almacenamiento de contraseñas mediante hashing bcrypt con factor de costo 12; protección CSRF en todos los formularios; validación de archivos cargados mediante inspección de bytes mágicos y no solo por extensión o tipo MIME declarado; limitación de intentos de autenticación en los endpoints de inicio de sesión; mensajes de error genéricos que no revelan información sensible; y verificación de permisos en el servidor (backend) de forma independiente a las restricciones de la interfaz de usuario.</p>
        <p>El Usuario se compromete a no intentar eludir, deshabilitar o interferir con estas medidas. Cualquier vulnerabilidad detectada deberá ser reportada responsablemente al Titular antes de su divulgación pública.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">14. Menores de Edad</h2>
        <p>La Plataforma está dirigida a usuarios mayores de dieciocho (18) años. Los menores de edad solo podrán hacer uso de los servicios con la autorización expresa de sus padres o representantes legales, quienes asumirán plena responsabilidad por las acciones realizadas a través de la cuenta. ODI3D no recauda conscientemente datos personales de menores de edad sin dicha autorización, en cumplimiento del Artículo 7 de la Ley 1581 de 2012.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">15. Ley Aplicable y Jurisdicción</h2>
        <p class="mb-3">Los presentes Términos y Condiciones se rigen por las leyes de la República de Colombia. Las partes acuerdan que cualquier controversia derivada de la interpretación, cumplimiento o ejecución del presente documento se someterá en primera instancia a mecanismos alternativos de solución de conflictos como conciliación o mediación, de conformidad con la Ley 640 de 2001. De no llegarse a un acuerdo, la jurisdicción competente será la de los jueces y tribunales del municipio de Aguachica, Cesar, Colombia.</p>
        <p>Para asuntos relacionados con protección de datos personales, el Usuario podrá dirigirse a la Superintendencia de Industria y Comercio (SIC) como autoridad de control, de conformidad con el Artículo 19 de la Ley 1581 de 2012. Para reclamaciones de consumidor aplican las disposiciones de la Ley 1480 de 2011 y los canales definidos por la SIC.</p>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">16. Información de Contacto</h2>
        <p class="mb-3">Para ejercer los derechos consagrados en la Ley 1581 de 2012, realizar consultas, solicitar actualización o supresión de datos, o presentar cualquier reclamación relacionada con estos Términos, el Usuario puede contactar a ODI3D a través de los siguientes canales:</p>
        <ul class="space-y-1.5 list-none pl-0">
          <li><span class="text-odi-texto font-medium">Titular:</span> Juan David Ortiz Vera</li>
          <li><span class="text-odi-texto font-medium">Marca:</span> ODI3D — Ortiz Diseño e Impresión 3D</li>
          <li><span class="text-odi-texto font-medium">Ubicación:</span> Aguachica, Cesar, Colombia</li>
          <li><span class="text-odi-texto font-medium">Canal principal:</span> WhatsApp Business (enlace disponible en la Plataforma)</li>
          <li><span class="text-odi-texto font-medium">Canal secundario:</span> Instagram — perfil oficial de ODI3D</li>
          <li><span class="text-odi-texto font-medium">Horario de atención:</span> Lunes a sábado, horario hábil colombiano</li>
        </ul>
      </section>

      <section>
        <h2 class="text-lg font-display font-semibold text-odi-texto mb-3">17. Disposiciones Finales</h2>
        <p>Si alguna cláusula de los presentes Términos fuere declarada nula o inaplicable por autoridad competente, las demás cláusulas continuarán vigentes y surtirán plenos efectos jurídicos. La falta de ejercicio por parte de ODI3D de cualquier derecho o acción conferida por estos Términos no deberá interpretarse como renuncia a tales derechos. Los presentes Términos y Condiciones constituyen el acuerdo integral entre ODI3D y el Usuario respecto al uso de la Plataforma, y reemplazan cualquier comunicación o acuerdo anterior sobre el mismo objeto.</p>
      </section>

    </article>

    <footer class="mt-12 pt-6 border-t border-odi-gris2 text-center text-odi-muted text-xs space-y-1">
      <p>Versión 1.0 &nbsp;|&nbsp; Junio 2026 &nbsp;|&nbsp; Aguachica, Cesar, Colombia</p>
      <p>ODI3D — Ortiz Diseño e Impresión 3D</p>
      <p>Documento elaborado como parte del proyecto de grado — Universidad Popular del Cesar, Secc. Aguachica</p>
    </footer>
  </div>
</template>
