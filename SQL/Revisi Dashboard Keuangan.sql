SELECT DISTINCT 
	a.RegistrationNo,
	a.MedicalNo,
	a.PatientName,
	a.BusinessPartnerName,
	r.CustomerType, 
	r.ChargeClassName,
	a.BedCode,
	a.BedStatus,
	a.ParamedicName,
	RencanaPulang = 
	CASE 
		WHEN cv.PlanDischargeTime IS NULL
		THEN  CAST(cv.PlanDischargeDate as VARCHAR) + CAST(cv.PlanDischargeTime AS VARCHAR)
		ELSE CAST(cv.PlanDischargeDate as DATETIME) + CAST(cv.PlanDischargeTime AS TIME)
    END,
	cv.GCPlanDischargeNotesType,
	Keterangan=
	CASE 
		WHEN sc.StandardCodeName = '' or sc.StandardCodeName IS NULL
		THEN  ''
		ELSE sc.StandardCodeName
    END,
	pvn.GCNoteType,
	pvn.NoteText,
	a.RegistrationID
FROM vBed a
LEFT JOIN vPatient		p	ON p.MRN=a.MRN
LEFT JOIN PatientNotes	pn	ON pn.MRN=a.MRN
LEFT JOIN vRegistration	r	ON r.RegistrationID=a.RegistrationID
LEFT JOIN ConsultVisit  cv	ON cv.VisitID=r.VisitID
LEFT JOIN StandardCode  sc	ON sc.StandardCodeID=cv.GCPlanDischargeNotesType
LEFT JOIN PatientVisitNote	pvn	ON pvn.VisitID=cv.VisitID and pvn.GCNoteType='X312^003'
WHERE a.IsDeleted=0 and a.RegistrationID IS NOT NULL
AND cv.PlanDischargeDate  IS NOT NULL
AND r.GCRegistrationStatus<>'X020^006' -- Pendaftaran Tidak DiBatalkan
ORDER BY a.BedCode