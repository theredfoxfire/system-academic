<?php

namespace App\Http\Controllers;

use App\ClassRoom;
use App\Teacher;
use App\Student;
use App\Subject;
use App\SchoolInfo;
use App\AcademicYear;
use App\ExamType;
use PDF;

class PDFController extends Controller
{
    //
    function byClass($class_id, $subject_id, $academic_year_id)
    {
        $class_room = ClassRoom::whereHas('subjects', function ($query) use ($subject_id) {
            $query->where('subject.id', $subject_id);
        })->whereHas('academic_years', function ($query) use ($academic_year_id) {
            $query->where('academic_year.id', $academic_year_id);
        })->with([
            'subjects' => function ($query) use ($subject_id) {
                $query->where('subject.id', $subject_id);
            },
            'academic_years' => function ($query) use ($academic_year_id) {
                $query->where('academic_year.id', $academic_year_id);
            },
            'subjects.exams' => function ($query) {
                $query->orderBy('exam.id', 'ASC');
            },
            'subjects.exams.examType',
            'subjects.exams.examPoints',
            'students'
        ])->find($class_id);

        $students = [];
        $TH = [];
        $UH = [];
        $th_total = 0;
        $uh_total = 0;

        // collect data
        if (!empty($class_room->students)) {
            foreach ($class_room->students as $student) {
                foreach ($class_room->subjects as $subject) {
                    foreach ($subject->exams as $exam) {
                        if ($exam->examType->name == 'Tugas Harian') {
                            foreach ($exam->examPoints as $examPoint) {
                                if ($examPoint->student_id == $student->id) {
                                    $TH[] = [
                                        'subject' => $subject->name,
                                        'name' => $exam->name,
                                        'scale' => $exam->examType->scale,
                                        'point' => $examPoint->point,
                                    ];
                                    $th_total += $examPoint->point;
                                }
                            }
                        }
                        if ($exam->examType->name == 'Ulangan Harian') {
                            foreach ($exam->examPoints as $examPoint) {
                                if ($examPoint->student_id == $student->id) {
                                    $UH[] = [
                                        'subject' => $subject->name,
                                        'name' => $exam->name,
                                        'scale' => $exam->examType->scale,
                                        'point' => $examPoint->point,
                                    ];
                                    $uh_total += $examPoint->point;
                                }
                            }
                        }
                    }
                }

                // handle empty value
                if (count($TH) != 0) {
                    $th_average = round($th_total / count($TH), 0);
                } else {
                    $th_average = 0;
                }
                if (count($UH) != 0) {
                    $uh_average = round($uh_total / count($UH), 0);
                } else {
                    $uh_average = 0;
                }

                // insert students
                $students[] = [
                    'id' => $student->serial,
                    'name' => $student->name,
                    'TH' => $TH,
                    'th_average' => $th_average,
                    'UH' => $UH,
                    'uh_average' => $uh_average,
                    'NA' => round(($th_average + $uh_average) / 2, 0)
                ];

                //reset value
                $TH = [];
                $UH = [];
                $th_total = 0;
                $uh_total = 0;
            }

            $data = [
                'class_room' => $class_room->name,
                'subject' => $class_room->subjects[0]->name,
                'academic_year' => $class_room->academic_years[0]->year,
                'students' => $students
            ];

            //return view('PDF.byClass', ['data' => $data]);
            $pdf = PDF::loadView('PDF.byClass', ['data' => $data])->setPaper('a3', 'landscape');

            return $pdf->download(ucfirst($data['class_room']) . '_' . $data['subject'] . '_' . $data['academic_year'] . '.pdf');
        } else {
            dd('Data is not available!');
        }
    }

    function penyebut($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->penyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}

    function byStudent($student_id, $academic_year)
    {
        $lastAcademicYear = $academic_year == 0 ? AcademicYear::max('id') : $academic_year;
        $examType = ExamType::where('is_deleted', '!=', 1)->orWhereNull('is_deleted')->get();
        $data = ClassRoom::whereHas('students', function ($query) use ($student_id) {
            $query->where('student.id', $student_id);
        })->with([
            'teachers',
            'students' => function ($query) use ($student_id) {
                $query->where('student.id', $student_id);
            },
            'classSubjects.academicYear' => function ($query) use ($lastAcademicYear) {
                $query->where('academic_year.id', $lastAcademicYear);
            },
            'classSubjects.exams' => function ($query) {
                $query->orderBy('exam.id', 'ASC');
            },
            'classSubjects.exams.examType',
            'classSubjects.exams.examPoints' => function ($query) use ($student_id) {
                $query->where('exam_point.student_id', $student_id);
                $query->orderBy('exam_point.id', 'ASC');
            }
        ])->first();
        $schoolInfo = SchoolInfo::first();

        // check existing guardian
        if (!empty($data->guardian_id)) {
            $guardian = Teacher::find($data->guardian_id)->name;
        } else {
            $guardian = '';
        }

        
        $subjects = [];
        
        foreach ($data->classSubjects as $key => $subject) {
            $final_score = 0;
            $score_by_scale = [];
            $data_nilai = [];
            $pointItemList = [];
            $pointCounterList = [];
            $pointScaleList = [];
            $passingPoint = $subject->passing_point ?? 0;
            foreach ($subject->exams as $exam) {
                if (!empty($exam->examPoints)) {
                    foreach ($exam->examPoints as $examPoint) {
                        $data_nilai[] = [
                            'point' => $examPoint->point,
                            'scale' => $exam->examType->scale,
                            'type' => $exam->examType->id,
                        ];
                    }
                }
            }

            foreach ($data_nilai as $nilai) {
                if (!empty($pointItem[$nilai['type']])) {
                    $pointItemList[$nilai['type']] = $pointItemList[$nilai['type']] + $nilai['point'];
                    $pointCounterList[$nilai['type']] = $pointCounterList[$nilai['type']] + 1;
                    $pointScaleList[$nilai['type']] = $nilai['scale'];
                } else {
                    $pointItemList[$nilai['type']] = $nilai['point'];
                    $pointCounterList[$nilai['type']] = 1;
                    $pointScaleList[$nilai['type']] = $nilai['scale'];  
                } 
            }

            $final_score = 0;
            foreach ($pointItemList as $index => $pointItem) {
                $calculateScore = $pointItem != 0 ? (($pointItem / $pointCounterList[$index]) * $pointScaleList[$index]) / 100 : 0;
                $final_score = $final_score + $calculateScore;
            }

            $in_words = "";
            $in_words = $this->penyebut(round($final_score, 0));
            $passingText = round($final_score, 0) >= $passingPoint ? "Tuntas" : "Tidak Tuntas";
            $desc = $final_score > 0 ? $passingText : '-' ;
            $subjects[] = [
                'subject' => $subject->subject->name,
                'final_score' => $final_score,
                'passingPoint' => $passingPoint,
                'in_words' => $final_score > 0 ? $in_words : "-",
                'description' => $desc,
            ];
        }

        $student = [
            'class' => $data->name,
            'name' => $data->students[0]->name,
            'serial' => $data->students[0]->serial,
            'attendance' => $data->students[0]->id,
            'score' => $subjects
        ];
        //dd($student);

        //return view('PDF.byStudent', ['data' => $student, 'guardian' => $guardian, 'subject' => $student['score'][0]['subject']]);
        $pdf = PDF::loadView('PDF.byStudent', ['data' => $student,'schoolInfo' => $schoolInfo, 'guardian' => $guardian, 'subject' => $student['score'][0]['subject']])->setPaper('a3', 'portrait');

        return $pdf->download($student['name'] . '_' . ucfirst($student['class']) . '.pdf');
    }

    function byStudentAllSubject($student_id)
    {
        $data = ClassRoom::whereHas('students', function ($query) use ($student_id) {
            $query->where('student.id', $student_id);
        })->with([
            'students' => function ($query) use ($student_id) {
                $query->where('student.id', $student_id);
            },
            'subjects.exams' => function ($query) {
                $query->orderBy('exam.id', 'ASC');
            },
            'subjects.exams.examType',
            'subjects.exams.examPoints' => function ($query) use ($student_id) {
                $query->where('exam_point.student_id', $student_id);
            },
        ])->first();
        //dd($data->subjects[0]->exams);

        $student = [];
        $subjects = [];
        $exams = [];
        $TH = [];
        $UH = [];
        $totalTH = 0;
        $totalUH = 0;

        if (!empty($data->subjects)) {
            foreach ($data->subjects as $subject) {
                foreach ($subject->exams as $exam) {
                    if (!empty($exam->examPoints[0])) {
                        if ($exam->examType->name == 'Tugas Harian') {
                            $TH[] = [
                                'type' => $exam->examType->name,
                                'point' => $exam->examPoints[0]->point
                            ];
                            $totalTH += $exam->examPoints[0]->point;
                        } elseif ($exam->examType->name == 'Ulangan Harian') {
                            $UH[] = [
                                'type' => $exam->examType->name,
                                'point' => $exam->examPoints[0]->point
                            ];
                            $totalUH += $exam->examPoints[0]->point;
                        }
                    }
                }
                $exams = [
                    'TH' => $TH,
                    'UH' => $UH,
                    'NA' => round((($totalTH / count($TH) + $totalUH / count($UH)) / 2), 0),
                    'averageTH' => round($totalTH / count($TH), 0),
                    'averageUH' => round($totalUH / count($UH), 0)
                ];

                $subjects[] = [
                    'name' => $subject->name,
                    'serial' => $subject->serial,
                    'exams' => $exams
                ];

                $TH = [];
                $UH = [];
                $totalTH = 0;
                $totalUH = 0;
            }
            $student = [
                'class' => $data->name,
                'name' => $data->students[0]->name,
                'subjects' => $subjects
            ];
            //dd($student);

            //return view('PDF.byStudentAllSubject', ['data' => $student]);

            $pdf = PDF::loadView('PDF.byStudentAllSubject', ['data' => $student])->setPaper('a3', 'landscape');

            return $pdf->download($student['name'] . '_' . ucfirst($student['class']) . '.pdf');
        } else {
            dd('Data is not available!');
        }
    }

    function byClassBulk($class_id)
    {
        $data = [];
        $students = [];
        $studentsList = Student::where('class_room_id', $class_id)->get();

        foreach ($studentsList as $student) {
            $student_id = $student->id;
            $data[] = ClassRoom::whereHas('students', function ($query) use ($student_id) {
                $query->where('student.id', $student_id);
            })->with([
                'teachers',
                'students' => function ($query) use ($student_id) {
                    $query->where('student.id', $student_id);
                },
                'subjects.exams' => function ($query) {
                    $query->orderBy('exam.id', 'ASC');
                },
                'subjects.exams.examType',
                'subjects.exams.examPoints' => function ($query) use ($student_id) {
                    $query->where('exam_point.student_id', $student_id);
                }
            ])->first();
        }

        foreach ($data as $singleData) {
            $TH = 0;
            $UH = 0;
            $UTS = 0;
            $UAS = 0;
            $THCount = 0;
            $UHCount = 0;
            $UTSCount = 0;
            $UASCount = 0;
            $THScale = 0;
            $THScale = 0;
            $UTSScale = 0;
            $UASScale = 0;
            $subjects = [];
            $data_nilai = [];
            $score_by_scale = [];
            $final_score = 0;
            foreach ($singleData->subjects as $subject) {
                foreach ($subject->exams as $exam) {
                    if (!empty($exam->examPoints)) {
                        foreach ($exam->examPoints as $examPoint) {
                            $data_nilai[] = [
                                'point' => $examPoint->point,
                                'scale' => $exam->examType->scale,
                                'type' => $exam->examType->name
                            ];
                        }
                    }
                }

                foreach ($data_nilai as $nilai) {
                    if ($nilai['type'] == 'Tugas Harian') {
                        $TH += $nilai['point'];
                        $THCount += 1;
                        $THScale = $nilai['scale'];
                    } else if ($nilai['type'] == 'Ulangan Harian') {
                        $UH += $nilai['point'];
                        $UHCount += 1;
                        $UHScale = $nilai['scale'];
                    } else if ($nilai['type'] == 'Ujian Tengah Semester') {
                        $UTS += $nilai['point'];
                        $UTSCount += 1;
                        $UTSScale = $nilai['scale'];
                    } else if ($nilai['type'] == 'Ujian Ahir Semester') {
                        $UAS += $nilai['point'];
                        $UASCount += 1;
                        $UASScale = $nilai['scale'];
                    }
                }

                $array_nilai = [
                    'TH' => $TH != 0 ? (($TH / $THCount) * $THScale) / 100 : 0,
                    'UH' => $UH != 0 ? (($UH / $UHCount) * $UHScale) / 100 : 0,
                    'UTS' => $UTS != 0 ? (($UTS / $UTSCount) * $UTSScale) / 100 : 0,
                    'UAS' => $UAS != 0 ? (($UAS / $UASCount) * $UASScale) / 100 : 0,
                ];
                $final_score = $array_nilai['TH'] + $array_nilai['UH'] + $array_nilai['UTS'] + $array_nilai['UAS'];

                // convert number to word
                $in_words = "";
                // $fmt = numfmt_create("ID", NumberFormatter::SPELLOUT);
                // $in_words = numfmt_format($fmt, round($final_score, 0));

                $subjects[] = [
                    'subject' => $subject->name,
                    'final_score' => $final_score,
                    'in_words' => $in_words,
                    'description' => round($final_score, 0) >= 75 ? "Tuntas" : "Tidak Tuntas"
                ];

                $final_score = 0;
                $score_by_scale = [];
                $data_nilai = [];
            }

            $students[] = [
                'class' => $singleData->name,
                'name' => $singleData->students[0]->name,
                'serial' => $singleData->students[0]->serial,
                'attendance' => $singleData->students[0]->id,
                'score' => $subjects
            ];
        }

        $guardian_id = ClassRoom::find($class_id);
        if (!empty($guardian_id->guardian_id)) {
            $guardian = Teacher::where('id', ClassRoom::find($class_id)->guardian_id)->first()->name;
        } else {
            $guardian = '';
        }

        //return view('PDF.byClassBulk', ['data' => $students, 'guardian' => $guardian]);
        $pdf = PDF::loadView('PDF.byClassBulk', ['data' => $students, 'guardian' => $guardian])->setPaper('a3', 'portrait');

        return $pdf->download('Nilai_Siswa_' . ucfirst($students[0]['class']) . '.pdf');
    }
}
