<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contract;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // English Contract
        Contract::create([
            'version' => '1.0-en',
            'language' => 'en',
            'title' => 'SUBSCRIPTION AND SALES TERMS - NETONYOU',
            'content' => "SUBSCRIPTION AND SALES TERMS
NETONYOU
Effective date: upon subscription to our digital magazines, for an amount of 39.90 USDT/USDC.

Website: https://www.netonyou.com | Contact: info@netonyou.com

IMPORTANT: Please read this contract carefully before completing the payment of 39.90 USDT/USDC. By clicking \"Pay/Confirm\" you declare that you have read and fully accepted these Terms, as well as the attached policies.

1. Purpose
This contract governs the access of the user (the \"User\") to the NETONYOU platform (\"the Platform\") and the acquisition of a digital subscription to NETONYOU's editorial content (digital magazines), as well as the conditions for participation in the sales commission plan (the \"Compensation Plan\"), as described in this document and its Annexes.

2. Identity and Contact
NETONYOU is the trade name of the platform indicated in this document. For any queries or notifications, you may write to info@netonyou.com. Communications related to your account will be made by email to the address provided by the User during registration.

3. Description of the Digital Product
The subscription grants access to exclusive digital magazines published by NETONYOU. Content is delivered in digital format with an approximate frequency of one issue every two months, for a total of 12 issues over a two (2) year period.

4. Price, Payment Method and Renewal
4.1. Price: The subscription price is 39.90 USDT/USDC (\"the Subscription Fee\"). The User is responsible for any network fees or other charges applicable from the payment provider or the relevant blockchain.

4.2. Payment Method: Payment is processed through a payment gateway compatible with crypto-assets (e.g., CoinPayments or equivalent). Upon completion of payment, access to digital content will be enabled in the User's account.

4.3. Biennial Renewal: The subscription is valid for two (2) years. To maintain access to the Platform and active User status for the purposes of the Compensation Plan, the User must renew by paying the Subscription Fee again at the end of each two-year period.

5. Compensation Plan and Minimum Activity
5.1. Nature of the plan: The Compensation Plan remunerates exclusively real subscription sales according to the plan in effect as published in the BackOffice. NETONYOU does not guarantee income or specific results. Amounts received depend on the effective commercial activity of the User and their organization.

5.2. Minimum activity: To accrue commissions in a given month, the User must make at least one (1) direct subscription sale during that month (\"Activity Rule\"). If this is not met, no commissions will accrue for that period and there will be no retroactive commissions.

5.3. Accrual and payment schedule: Commissions corresponding to the previous month's sales are settled between the 1st and the 10th of the following month. Unless otherwise indicated, the payment process may be carried out manually by the platform administrators.

6. User Obligations
6.1. The User agrees to provide accurate and truthful information during registration and to keep such information updated.

6.2. The User is responsible for maintaining the confidentiality of their account credentials and for all activities conducted under their account.

6.3. The User agrees not to share, resell, or redistribute the digital content provided through the subscription.

7. Platform Access and Usage
7.1. Access to the platform is granted solely for personal, non-commercial use.

7.2. The User may not attempt to circumvent any security measures or access restrictions implemented by the platform.

7.3. NETONYOU reserves the right to suspend or terminate access for violations of these terms.

8. Limitation of Liability
8.1. NETONYOU provides the platform and content \"as is\" without warranties of any kind.

8.2. NETONYOU shall not be liable for any indirect, incidental, or consequential damages arising from the use of the platform.

9. Modifications to Terms
9.1. NETONYOU reserves the right to modify these terms at any time.

9.2. Users will be notified of any material changes via email.

9.3. Continued use of the platform after changes constitutes acceptance of the new terms.

10. Governing Law and Jurisdiction
10.1. This contract is governed by the laws of the jurisdiction where NETONYOU operates.

10.2. Any disputes shall be resolved through arbitration or the appropriate courts of jurisdiction.

11. Contact Information
For any questions regarding this contract or the platform services, please contact:
Email: info@netonyou.com
Website: https://www.netonyou.com

By accepting these terms, you acknowledge that you have read, understood, and agree to be bound by all the conditions outlined in this contract.",
            'is_active' => true,
            'effective_date' => Carbon::now(),
        ]);

        // Spanish Contract
        Contract::create([
            'version' => '1.0-es',
            'language' => 'es',
            'title' => 'TÉRMINOS DE SUSCRIPCIÓN Y VENTAS - NETONYOU',
            'content' => "TÉRMINOS DE SUSCRIPCIÓN Y VENTAS
NETONYOU
Fecha de entrada en vigor, en el momento de la suscripción a nuestras revistas digitales, de importe igual a 39.90 USDT/USDC.

Sitio web: https://www.netonyou.com | Contacto: info@netonyou.com

IMPORTANTE: Lea atentamente este contrato antes de completar el pago de 39,90 USDT/USDC. Al pulsar \"Pagar/Confirmar\" usted declara haber leído y aceptado íntegramente estos Términos, así como las políticas anexas.

1. Objeto
El presente contrato regula el acceso del usuario (el \"Usuario\") a la plataforma NETONYOU (\"la Plataforma\") y la adquisición de una suscripción digital al contenido editorial (revistas digitales) de NETONYOU, así como las condiciones para la participación en el plan de comisiones por ventas (el \"Plan de Compensación\"), conforme a lo descrito en este documento y en sus Anexos.

2. Identidad y Contacto
NETONYOU es la marca comercial de la plataforma indicada en el presente documento. Para cualquier consulta o notificación, puede escribir a info@netonyou.com. Las comunicaciones relacionadas con su cuenta se realizarán por correo electrónico a la dirección facilitada por el Usuario en su registro.

3. Descripción del Producto Digital
La suscripción otorga acceso a revistas digitales exclusivas publicadas por NETONYOU. El contenido se entrega en formato digital con una periodicidad aproximada de una edición cada dos meses, hasta completar un total de 12 ediciones en el periodo de dos (2) años.

4. Precio, Forma de Pago y Renovación
4.1. Precio: El precio de la suscripción es de 39,90 USDT/USDC (\"la Cuota de Suscripción\"). El Usuario es responsable de cualquier comisión de red u otros cargos aplicables del proveedor de pagos o de la blockchain correspondiente.

4.2. Forma de pago: El pago se procesa mediante pasarela de pago compatible con criptoactivos (por ejemplo, CoinPayments u otra equivalente). Al completar el pago, el acceso al contenido digital será habilitado en la cuenta del Usuario.

4.3. Renovación bianual: La suscripción tiene una duración de dos (2) años. Para mantener el acceso a la Plataforma y la condición de Usuario activo a efectos del Plan de Compensación, el Usuario deberá renovar abonando nuevamente la Cuota de Suscripción al finalizar cada periodo bianual.

5. Plan de Compensación y Actividad Mínima
5.1. Naturaleza del plan: El Plan de Compensación retribuye exclusivamente las ventas reales de suscripciones conforme al Plan vigente publicado en el BackOffice. NETONYOU no garantiza ingresos ni resultados determinados. Los importes percibidos dependen de la actividad comercial efectiva del Usuario y su organización.

5.2. Actividad mínima: Para devengar comisiones en un mes determinado, el Usuario debe realizar al menos una (1) venta directa de suscripción durante dicho mes (\"Regla de Actividad\"). Si no se cumple, no se devengan comisiones de ese periodo y no existen comisiones retroactivas.

5.3. Calendario de devengo y pagos: Las comisiones correspondientes a ventas del mes anterior se liquidan entre el día 1 y el día 10 del mes siguiente. Salvo indicación contraria, el proceso de pago puede realizarse manualmente por los administradores de la plataforma.

6. Obligaciones del Usuario
6.1. El Usuario se compromete a proporcionar información precisa y veraz durante el registro y a mantener dicha información actualizada.

6.2. El Usuario es responsable de mantener la confidencialidad de sus credenciales de cuenta y de todas las actividades realizadas bajo su cuenta.

6.3. El Usuario se compromete a no compartir, revender o redistribuir el contenido digital proporcionado a través de la suscripción.

7. Acceso y Uso de la Plataforma
7.1. El acceso a la plataforma se otorga únicamente para uso personal y no comercial.

7.2. El Usuario no puede intentar eludir ninguna medida de seguridad o restricción de acceso implementada por la plataforma.

7.3. NETONYOU se reserva el derecho de suspender o terminar el acceso por violaciones de estos términos.

8. Limitación de Responsabilidad
8.1. NETONYOU proporciona la plataforma y el contenido \"tal como está\" sin garantías de ningún tipo.

8.2. NETONYOU no será responsable por daños indirectos, incidentales o consecuentes que surjan del uso de la plataforma.

9. Modificaciones de los Términos
9.1. NETONYOU se reserva el derecho de modificar estos términos en cualquier momento.

9.2. Los Usuarios serán notificados de cualquier cambio material por correo electrónico.

9.3. El uso continuado de la plataforma después de los cambios constituye la aceptación de los nuevos términos.

10. Ley Aplicable y Jurisdicción
10.1. Este contrato se rige por las leyes de la jurisdicción donde opera NETONYOU.

10.2. Cualquier disputa será resuelta a través de arbitraje o los tribunales apropiados de jurisdicción.

11. Información de Contacto
Para cualquier pregunta sobre este contrato o los servicios de la plataforma, por favor contacte:
Email: info@netonyou.com
Sitio web: https://www.netonyou.com

Al aceptar estos términos, usted reconoce que ha leído, entendido y acepta estar sujeto a todas las condiciones descritas en este contrato.",
            'is_active' => true,
            'effective_date' => Carbon::now(),
        ]);

        $this->command->info('Contracts seeded successfully!');
    }
}
