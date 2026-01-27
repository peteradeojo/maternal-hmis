@props(['patient'])

<form action="?consent-form" method="post" id="consent-form">
    <div class="grid gap-y-2">
        <h2 class="basic-header">PATIENT INFORMED CONSENT FORM</h2>

        <p>I, the undersigned: <x-input-text name="name" placeholder="Your name" required /> </p>

        <p>
            Give consent to the following :<br />
            <x-input-text name="procedure" placeholder="Procedure to be performed" required /> <br />

            Operation/procedure/treatment/process, upon myself/my spouse/ my dependent<br />

            <x-input-text name="patient" value="{{ $patient->name }}" readonly /> <br />
        </p>

        <p>1. My surgeon has provided me with a general explanation of the nature of this
            operation/procedure/treatment/process
            and the reasons for its indication based on my particular medical condition.</p>
        <p>2. My surgeon has also discussed with me the risks and benefits of the operation/procedure/treatment/process.
            Some of
            these risks include, but are not limited to, the following: <br /><br />
            <strong>General surgical complications:</strong> infection, allergic reactions, wound breakdown, nerve and
            blood
            vessel injury,
            haematoma, DVT, Pulmonary embolism, blood loss requiring transfusion, prolonged hospitalization, loss of
            limb,
            death.
        </p>

        <p>
            3. My surgeon has also explained that I can generally expect the following possible consequences and
            complications
            as a natural result of the risk of undergoing intervention (some of which are attendant to an invasive
            procedure).
            Although some of these may not occur, including but not limited to, the following:<br /><br />

            <strong>Specific complications:</strong> failure of relief of symptoms, dislocation/instability, fracture,
            heterotrophic bone formation, stiffness due to scarring, traction neuropraxia, implant failure,
            instrumentation
            failure and implant malposition, limb length inequality, non-union, Avascular Necrosis, non-resolution or
            recurrence
            of symptoms.
        </p>

        <p>
            4. My surgeon has explained alternatives to undergoing this operation/treatment/procedure/process including
            alternative operative measures that may be deemed necessary or desirable during the course of this
            operation/procedure/treatment/process, also inclusive of:<br /><br />

            <em>Reoperation due to unforeseen complications arising from surgery.</em>
        </p>

        <p>5. I furthermore grant consent to the administration of a general or other anaesthetic for the purposes of
            the
            said
            operation/procedure/treatment/process or alternative operative procedures. I moreover hereby grant consent
            to
            any
            radiological or diagnostic examination/laboratory tests/hospital services that are medically indicated or
            that
            the
            doctors may prescribe.</p>

        <p>
            <strong>6. Blood transfusion</strong><br />

            I hereby consent to a blood/blood product transfusion to myself/the patient upon the instruction of the said
            medical
            practitioner if deemed medically indicated.
        </p>

        <p>7. My surgeon has also explained to me that other physicians and health care providers may participate in my
            care. I
            therefore extend this authorization to these other physicians and health care providers. Although unlikely,
            in
            the
            event that my physician is not available to perform the above operation/procedure/treatment/process, I
            understand
            that this authorization is extended to them. If possible, however, I will be notified of the substitution
            for
            rescheduling if needed.</p>

        <p>
            9. I agree that any medical/scientific data obtained from my operation/procedure/treatment/process can be
            used
            anonymously for the furtherance of medical care by way of study/presentation/publication or review for
            outcomes
            assessment by Maternal-Child Specialists' Clinics. I understand that this consent for collection of data for
            research is
            voluntary, that care will be unaffected whether consent is given or not. The data will be kept confidential
            and
            there will be no benefit to individual participants.
            {{-- Ethical guidelines for the management of medical data are
            provided by the Human Research Ethics Committee, part of the Faculty for Health Sciences, University of Cape Town,
            Rm E53-46 Old Main Building ,Groote Schuur Hospital, Observatory,7921,

            Tel: 021 4066626, Website: http://www.health.uct.ac.za/fhs/research/humanethics --}}



            {{-- Patient Signature:_________________________ Parent/Gardian:_______________________ --}}
        </p>


        <p>
            10. I acknowledge that I have been informed of my/ the patient's health status, the range of diagnostic
            procedures
            and treatments generally available to myself / the patient, the benefits, risks, surgical approximate costs
            and
            consequences generally associated with each option, my / the patient's right to refuse health services and
            the
            implications, risks and obligations of such refusal.
        </p>

        <p>11. I understand that the outcome of the surgery/procedure/treatment intervention/process is very dependent
            upon
            me/my dependent being compliant with respect to the post-operative instructions given verbally or in writing
            by
            the surgeon, the physiotherapists and/or his staff, and that the surgeon cannot be held liable if there are
            complications
            due to non-compliance.</p>

        <p>12. After discussing all of the above, my physician gave me an opportunity to ask questions and seek further
            information regarding to above items. I believe that I do not require further information at this time, and
            I am
            prepared to proceed with the recommended operation/treatment/procedure/process. I believe that my physician
            has
            honored my/ the patient's right to make my/the patient's own informed health care decision, give my consent
            voluntarily and freely, and certify that I can give valid consent. I understand that I can revoke this
            consent
            at
            any time up until the time the operation/treatment/procedure/process is started.</p>

        <p>13. I acknowledge that I/the patient have been informed of all the above in a language understood by me/the
            patient.
        </p>

        <p>14. I acknowledge that I am informed with respect to all aspects of medical risk related to pandemics, most
            recently
            <strong>COVID-19</strong>, and specifically the increased risk of complications associated with having
            surgery.
            This
            is especially relevant should I/the patient have been exposed to and/or am currently infected, whether
            symptomatic
            or not.
        </p>

        <div class="grid grid-cols-2 gap-y-4">
            <p class="col-span-full">SIGNED AT: Maternal-Child Specialists' Clinics<br />
                THIS {{ date('jS') }} DAY OF {{ strtoupper(date('F, Y')) }}</p>

            <div>
                <p>Signature of patient/parent/spouse/guardian/Curator/mandated person/ grandparent/adult
                    child/Brother/sister (Specify capacity of signatory)</p>

                <div class="grid gap-y-2">
                    <x-input-select name="relationship" required>
                        <option selected disabled>Please select a relationship</option>
                        <option value="Patient">Patient</option>
                        <option value="Parent">Parent</option>
                        <option value="Spouse">Spouse</option>
                        <option value="Sibling">Sibling</option>
                        <option value="Guardian">Guardian</option>
                        <option value="Curator">Curator</option>
                        <option value="Mandated person">Mandated person</option>
                        <option value="Grandparent">Grandparent</option>
                        <option value="Adult child">Adult Child</option>
                    </x-input-select>
                </div>

                <div class="grid gap-y-2">
                    <canvas id="emptyisignature" style="display: none" height="300"></canvas>
                    <canvas id="signature" height="300" class="border border-black"></canvas>
                    <div class="flex-center gap-x-4">
                        <button type="button" class="btn bg-gray-300" id="clear-signature">Clear</button>
                        <button type="button" class="btn bg-gray-300" id="save-signature">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <p class="basic-header">WITNESSES</p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p>Witness 1</p>
                <x-input-text name="witness[]" class="form-control" />
            </div>
            <div>
                <p>Witness 2</p>
                <x-input-text name="witness[]" class="form-control" />
            </div>
        </div>

    </div>

    <div class="form-group">
        <button type="submit" class="btn bg-primary">Submit</button>
    </div>
</form>
