<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .header-table { width: 100%; margin-bottom: 10px; }
        .header-table td { vertical-align: top; }
        .titre-box { border: 1px solid #1f2937; text-align: center; padding: 10px; margin: 15px 0; }
        .titre-box h1 { font-size: 20px; margin: 0; letter-spacing: 1px; }
        h2 { font-size: 12px; margin-top: 14px; margin-bottom: 5px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        h3.article { font-size: 11.5px; margin-top: 12px; margin-bottom: 4px; }
        p { margin: 3px 0; line-height: 1.45; text-align: justify; }
        ul, ol { margin: 4px 0 4px 20px; padding: 0; }
        li { margin-bottom: 4px; text-align: justify; }
        .party-block { margin-bottom: 8px; }
        .party-block .label { font-weight: bold; text-decoration: underline; }
        .signatures { margin-top: 40px; width: 100%; }
        .signatures td { width: 50%; vertical-align: top; padding-top: 30px; }
        .sig-token { margin-top: 6px; font-size: 11px; color: #6b7280; }
        .footnote { font-size: 9px; color: #6b7280; margin-top: 15px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 70%;"><strong>DSKT</strong> - A new concept of lodging</td>
            <td style="width: 30%; text-align: right;">N° : {{ $contrat->numero }}</td>
        </tr>
    </table>

    <div class="titre-box">
        <h1>CONTRAT DE BAIL</h1>
    </div>

    <h2>OBJET ET PARTIES</h2>
    <p>Nature du bail : Ceci est un contrat de bail à usage {{ $contrat->nature_bail ?? "d'habitation" }}</p>

    <p><strong><u>ENTRE LES SOUSSIGNÉS :</u></strong></p>

    @php $manager = $contrat->appartement->immeuble->manager ?? null; @endphp
    <div class="party-block">
        <p>Nom et prénom du bailleur ou son mandataire : {{ $manager->name ?? '' }} {{ $manager->surname ?? '' }}</p>
        <p>CNI N° : {{ $manager->cni_number ?? '' }}</p>
        <p>Téléphone : {{ $manager->phone ?? '' }}</p>
        <p>Adresse email : {{ $manager->email ?? '' }}</p>
        <p>Lieu de résidence : {{ $manager->address ?? '' }}</p>
        <p>Ci-après dénommé(s) « le bailleur »</p>
    </div>
    <p><strong>D'UNE PART,</strong></p>

    <div class="party-block">
        <p>Nom et prénom du locataire : {{ $contrat->tenant->name ?? '' }} {{ $contrat->tenant->surname ?? '' }}</p>
        <p>CNI N° : {{ $contrat->tenant->cni_number ?? '' }}</p>
        <p>Téléphone : {{ $contrat->tenant->phone ?? '' }}</p>
        <p>Adresse email : {{ $contrat->tenant->email ?? '' }}</p>
        <p>Lieu de résidence : {{ $contrat->tenant->address ?? '' }}</p>
        <p>Profession : {{ $contrat->tenant->profession ?? '' }}</p>
        <p>Ci-après dénommé(s) « le locataire »</p>
    </div>
    <p><strong>D'AUTRE PART,</strong></p>

    @if($contrat->garant)
    <div class="party-block">
        <p>Nom et prénom du garant du locataire : {{ $contrat->garant->nom }}</p>
        <p>CNI N° : {{ $contrat->garant->cni_number ?? '' }}</p>
        <p>Téléphone : {{ $contrat->garant->telephone ?? '' }}</p>
        <p>Adresse email : {{ $contrat->garant->email ?? '' }}</p>
        <p>Lieu de résidence : {{ $contrat->garant->lieu_residence ?? '' }}</p>
        <p>Profession : {{ $contrat->garant->profession ?? '' }}</p>
    </div>
    @endif

    <p><strong><u>Il a été convenu et arrêté ce qui suit :</u></strong></p>
    <p>Le bailleur loue, dans les conditions prévues par la loi et par le présent contrat, au locataire qui les accepte, les locaux ci-après désignés.</p>

    <p>Désignation et consistance des locaux loués :</p>
    <p>La surface louée est {{ $contrat->appartement->description ?? "un logement à usage d'habitation" }}.</p>

    <p>Le locataire déclare bien connaître les lieux loués pour les avoir vus et visités. Il déclare également que le bailleur lui a remis lors de la signature du présent contrat un état des lieux établi dans les conditions définies ci-dessous.</p>
    <p>Le locataire déclare que le bailleur lui a communiqué, lors de la signature du présent contrat, les extraits du règlement intérieur de l'immeuble, la jouissance et l'usage des parties privatives et communes et précisant la quote-part afférente au lot loué dans chacune des catégories de charges.</p>

    <h3 class="article">Article 1 : ÉTAT DES LIEUX</h3>
    <p>Un état des lieux sera établi contradictoirement entre les parties au moment de la remise des clés au locataire ; il en sera de même lors de la restitution de celles-ci. À défaut, et sans mise en demeure préalable, cet état de lieu sera établi par huissier de justice à l'initiative de la partie la plus diligente.
    Un exemplaire de l'état des lieux est joint à l'exemplaire du présent contrat de location qui est remis à chaque partie.</p>

    <h3 class="article">Article 2 : DESTINATION</h3>
    <p>Les locaux loués sont destinés à un usage d'habitation.</p>

    <h3 class="article">Article 3 : OCCUPATION & JOUISSANCE</h3>
    <p><strong>Le bailleur s'engage à :</strong></p>
    <ol>
        <li>Délivrer au locataire les locaux en bon état d'usage et de réparations, ainsi que les équipements mentionnés au contrat en bon état de fonctionnement.</li>
        <li>Assurer au locataire la jouissance paisible des locaux loués ; toutefois, sa responsabilité ne pourra pas être recherchée en raison des voies de fait dont les autres locataires ou des tiers se rendraient coupables à l'égard du locataire.</li>
        <li>Entretenir les locaux en état de servir à l'usage prévu et y faire toutes les réparations nécessaires autres que locatives.</li>
        <li>Remettre gratuitement une quittance au locataire lorsqu'il en fait la demande.</li>
    </ol>

    <p><strong>Le locataire s'engage à :</strong></p>
    <ol>
        <li>Payer le loyer et les charges récupérables aux termes convenus. Le paiement mensuel est de droit s'il en fait la demande.</li>
        <li>User paisiblement des locaux et équipements loués suivant la destination prévue au contrat. En particulier, il s'engage à respecter les stipulations prévues à cet égard par le règlement intérieur de l'immeuble dont il déclare avoir pris connaissance.</li>
        <li>Répondre des dégradations et pertes survenant pendant la durée du contrat dans les locaux dont il a la jouissance exclusive, à moins qu'il ne prouve qu'elles aient eu lieu par la faute du bailleur.</li>
        <li>Prendre à sa charge l'entretien courant du logement, des équipements mentionnés au contrat et les menues réparations ainsi que l'ensemble des réparations locatives, sauf si elles sont occasionnées par vétusté.</li>
        <li>Ne pas transformer les locaux en un lieu de culte. Toute prière de groupe ou personnel de façon à nuire à la tranquillité des autres occupants est interdite.</li>
        <li>Ne pas céder le contrat de location, ni sous-louer le local sauf avec l'accord écrit du bailleur, y compris sur le prix du loyer. En cas de cessation du contrat principal, le sous-locataire ne pourra se prévaloir d'aucun droit à l'encontre du bailleur, ni d'aucun titre d'occupation.</li>
        <li>Laisser exécuter dans les lieux loués les travaux d'amélioration des parties communes ou des parties privatives du même immeuble, ainsi que les travaux nécessaires au maintien en état et à l'entretien normal des locaux loués, les dispositions des deuxième et troisième alinéas de l'article 1724 du Code civil étant applicables à ces travaux.</li>
        <li>Ne pas transformer les locaux et équipements loués sans l'accord écrit du propriétaire, lequel pourra subordonner cet accord et l'exécution des travaux à l'avis et à la surveillance d'un architecte de son choix, dont les honoraires seront payés par le locataire. En cas de méconnaissance par le locataire de cette obligation, le bailleur pourra exiger la remise en état des lieux ou des équipements au départ du locataire ou conserver les transformations effectuées, sans que le locataire puisse réclamer une indemnisation pour les frais engagés.
        Si les transformations opérées mettent en péril le bon fonctionnement des équipements ou la sécurité du local, le bailleur pourra exiger, aux frais du locataire, la remise immédiate des lieux en l'état.</li>
        <li>S'assurer contre les risques locatifs dont il doit répondre en sa qualité de locataire : incendie, dégât des eaux.</li>
        <li>Accepter la réalisation par le bailleur des réparations urgentes et qui ne peuvent être différées jusqu'à la fin du contrat de location ; conformément à l'article 1724 du Code civil. Si ces réparations durent plus de 40 jours, le loyer, à l'exclusion des charges, sera diminué en proportion du temps et de la partie de la chose louée dont le locataire aura été privé.</li>
        <li>Informer immédiatement le bailleur de tout sinistre et des dégradations se produisant dans les lieux loués, même s'il n'en résulte aucun dommage apparent.</li>
        <li>Laisser visiter les lieux loués, en vue de leur vente ou de leur location, deux heures par jour, au choix du bailleur, sauf les jours fériés.</li>
        <li>Acquitter toutes les contributions et taxes lui incombant personnellement (notamment la taxe d'habitation) de manière à ce que le bailleur ne soit pas inquiété à ce sujet. Le locataire devra, avant tout déménagement, justifier du paiement des impôts dont le bailleur pourrait être tenu responsable.</li>
        <li>Ne pas déménager, sans s'être conformé à ses obligations, ni sans avoir auparavant présenté au bailleur les quittances justifiant du paiement de ses obligations.</li>
        <li>Remettre au bailleur, dès son départ, toutes les clés des locaux loués et lui faire connaître sa nouvelle adresse.</li>
    </ol>

    <h3 class="article">Article 4 : DURÉE</h3>
    <p>Le présent contrat de location est conclu pour une durée renouvelable qui commence à courir le {{ $contrat->start_date->format('d/m/Y') }} et se termine le {{ $contrat->end_date->format('d/m/Y') }}.</p>

    <h3 class="article">Article 5 : RÉSILIATION ANTICIPÉE</h3>
    <p>Le présent contrat de location pourra être résilié par le locataire à tout moment. Le congé devra être notifié au bailleur dans les conditions fixées par la loi. Le bailleur pourra agir en résiliation anticipée du contrat, par la voie judiciaire, en cas de méconnaissance par le locataire de ses obligations et ce sans qu'il soit nécessaire que la demande en justice formée à cet effet soit précédée d'un congé.</p>
    <p>À défaut de congé ou de demande de renouvellement par l'une des parties, le présent contrat de location parvenu à son terme est renouvelé tacitement pour une durée au moins égale à 1 an.
    Par ailleurs, les parties s'engagent à s'informer deux (02) mois avant le terme du bail :</p>
    <ul>
        <li>Le bailleur pour son intention de mettre fin au contrat</li>
        <li>Le locataire pour son intention de libérer les locaux</li>
    </ul>

    <h3 class="article">Article 6 : CONGÉ</h3>
    <p>La partie qui entend user de son droit de résilier le présent contrat par anticipation ou de celui de refuser son renouvellement est tenue de notifier à l'autre un congé, par lettre recommandée avec demande d'avis de réception ou par acte d'huissier de justice.
    Le délai de préavis applicable au congé est de deux (02) mois.</p>

    <h3 class="article">Article 7 : LOYER</h3>
    <p>Le présent contrat de location est consenti et accepté moyennant le loyer mensuel de ({{ number_format($contrat->rent_amount, 2, ',', ' ') }} FCFA) hors taxes et hors charges qui sera payable d'avance le premier jour suivant le terme du précédent. Après la première année, le locataire procédera au paiement chaque deux mois, payable avant le terme du précédent.</p>

    <h3 class="article">Article 8 : RÉVISION</h3>
    <p>Le loyer fixé ci-dessus pourra être révisé par les parties à la date anniversaire du contrat.</p>

    <h3 class="article">Article 9 : CHARGES</h3>
    <p>Outre le loyer, le locataire devra s'acquitter mensuellement des factures d'électricité, d'eau et de câble. Il doit aussi rembourser au bailleur et, sur justification, les charges récupérables telles qu'elles sont définies par la loi. Les autres charges constituent les frais d'entretien des espaces communs, l'éclairage et le gardiennage.</p>

    <h3 class="article">Article 10 : PAIEMENT DU LOYER ET DES CHARGES</h3>
    <p>Le paiement des loyers et des charges se fera au domicile du bailleur.
    Le paiement peut se faire dans un compte bancaire indiqué par le bailleur, le locataire doit dans ce cas déposer une copie du bordereau de versement au bailleur.
    Si le locataire en fait la demande, le bailleur lui remettra une quittance, portant le détail des sommes versées en distinguant le loyer et les charges.</p>

    <h3 class="article">Article 11 : DÉPÔT DE GARANTIE & CAUTIONS<sup>1</sup></h3>
    <p>Pour garantir l'exécution de ses obligations, le locataire versera la somme de {{ number_format($contrat->deposit_amount, 2, ',', ' ') }} FCFA
    (payable au plus tard le {{ optional($contrat->deposit_due_date)->format('d/m/Y') ?? '____' }}). En cas de révision du loyer, le dépôt de garantie sera modifié de plein droit dans les mêmes proportions.</p>
    <p>Ce dépôt, non productif d'intérêts, est indépendant des loyers et charges, lesquels devront être régulièrement payés aux dates fixées, jusqu'au départ effectif du locataire.</p>

    <h3 class="article">Article 12 : CLAUSE RÉSOLUTOIRE ET CLAUSES PÉNALES</h3>
    <p>Le présent contrat sera résilié immédiatement et de plein droit sans qu'il soit besoin de faire ordonner cette résolution en justice : deux mois après un commandement demeuré infructueux à défaut de paiement aux termes convenus de tout ou partie du loyer et des charges dûment justifiées ou en cas de non-versement du dépôt de garantie éventuellement prévu au contrat ; un mois après un commandement demeuré infructueux à défaut d'assurance contre les risques locatifs.</p>
    <p>Lorsqu'une caution garantit les obligations du présent contrat, le commandement de payer est signifié à la caution dans un délai de quinze jours à compter de la signification du commandement au locataire. À défaut, la caution ne peut être tenue au paiement des pénalités ou intérêts de retard.</p>
    <p>Une fois acquis au bailleur le bénéfice de la clause résolutoire, le locataire devra libérer immédiatement les lieux ; s'il s'y refuse, son expulsion aura lieu sur simple ordonnance de référé.</p>

    <h3 class="article">Article 13 : SOLIDARITÉ ET INDIVISIBILITÉ</h3>
    <p>Pour l'exécution de toutes les obligations résultant du présent contrat, il y aura solidarité et indivisibilité entre les parties ci-dessus désignées par le terme de « locataire ». Par ailleurs, le locataire s'engage à faire connaître au bailleur toute modification de sa situation matrimoniale.</p>

    <p class="footnote"><sup>1</sup> Le locataire sur arrangement avec le bailleur est dispensé de cette obligation.</p>

    <p style="margin-top: 20px;">Fait à {{ $contrat->appartement->immeuble->town ?? '' }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }} en deux (02) exemplaires.</p>

    <table class="signatures">
        <tr>
            <td>
                <p>Le bailleur :</p>
                {{-- Documenso detecte ce jeton et place le champ de signature du destinataire n°1 (gestionnaire/admin) --}}
                <p class="sig-token">@verbatim{{signature, r1}}@endverbatim</p>
            </td>
            <td>
                <p>Le locataire :</p>
                {{-- Destinataire n°2 --}}
                <p class="sig-token">@verbatim{{signature, r2}}@endverbatim</p>
            </td>
        </tr>
    </table>

    @if($contrat->garant)
    <p style="margin-top: 20px;">La caution du locataire : {{ $contrat->garant->nom }}</p>
    @endif

</body>
</html>
