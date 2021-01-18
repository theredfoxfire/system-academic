<?php

namespace App\Http\Controllers;

use App\ClassRoom;
use Illuminate\Http\Request;
use App\Teacher;
use App\Student;
use NumberFormatter;
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
                        if ($exam->exam_type_id == 5) {
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
                        if ($exam->exam_type_id == 3) {
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

            //dd($data);
            ///return view('PDF.byClass', ['data' => $data]);
            $pdf = PDF::loadView('PDF.byClass', ['data' => $data])->setPaper('a3', 'landscape');

            return $pdf->download(ucfirst($data['class_room']) . '_' . $data['subject'] . '_' . $data['academic_year'] . '.pdf');
        } else {
            dd('The data is not available!');
        }
    }

    function byStudent($student_id)
    {
        $data = ClassRoom::whereHas('students', function ($query) use ($student_id) {
            $query->where('student.id', $student_id);
        })->with([
            'teachers',
            'students' => function ($query) use ($student_id) {
                $query->where('student.id', $student_id);
            },
            'subjects.exams' => function ($query) {
                $query->orderBy('exam.exam_type_id', 'DESC');
            },
            'subjects.exams.examType',
            'subjects.exams.examPoints' => function ($query) use ($student_id) {
                $query->where('exam_point.student_id', $student_id);
            }
        ])->first();
        $guardian = Teacher::where('id', $data->guardian_id)->first();

        $subjects = [];
        $data_nilai = [];
        $score_by_scale = [];
        $final_score = 0;
        foreach ($data->subjects as $subject) {
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

            $count = 0;
            foreach ($data_nilai as $key => $collect) {
                if (!empty($data_nilai[$key + 1])) {
                    if (!empty($score_by_scale)) {
                        if ($score_by_scale['type'] == $data_nilai[$key]['type']) {
                            $score_by_scale['point'] += $collect['point'];
                            $score_by_scale['count'] = ++$count;
                        } else {
                            $final_score += ((($score_by_scale['point']) / $count) * $data_nilai[$key - 1]['scale']) / 100;
                            $count = 0;
                            $score_by_scale = [
                                'type' => $collect['type'],
                                'point' => $collect['point'],
                                'count' => ++$count
                            ];
                        }
                    } else {
                        $score_by_scale = [
                            'type' => $collect['type'],
                            'point' => $collect['point'],
                            'count' => ++$count
                        ];
                    }
                } else {
                    if (!empty($score_by_scale)) {
                        if ($score_by_scale['type'] == $data_nilai[$key]['type']) {
                            $score_by_scale['point'] += $collect['point'];
                            $score_by_scale['count'] = ++$count;
                        } else {
                            $final_score += ((($score_by_scale['point']) / $count) * $data_nilai[$key - 1]['scale']) / 100;
                        }
                    }
                    $final_score += ($collect['point'] * $data_nilai[$key]['scale']) / 100;
                }
            }

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

        $student = [
            'class' => $data->name,
            'name' => $data->students[0]->name,
            'serial' => $data->students[0]->serial,
            'attendance' => $data->students[0]->id,
            'score' => $subjects
        ];

        return view('PDF.byStudent', ['data' => $student, 'guardian' => $guardian->name, 'subject' => $student['score'][0]['subject']]);
        $pdf = PDF::loadView('PDF.byStudent', ['data' => $student, 'guardian' => $guardian->name, 'subject' => $student['score'][0]['subject']])->setPaper('a3', 'portrait');

        return $pdf->download($student['name'] . '_' . $student['score'][0]['subject'] . '_' . ucfirst($student['class']) . '.pdf');
    }

    function byClassBulk($class_id)
    {
        $data = [];
        $students = [];
        //$student_id = 6;
        $studentsList = Student::where('class_room_id', $class_id)->get();
        // dd($students);

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
                    $query->orderBy('exam.exam_type_id', 'DESC');
                },
                'subjects.exams.examType',
                'subjects.exams.examPoints' => function ($query) use ($student_id) {
                    $query->where('exam_point.student_id', $student_id);
                }
            ])->first();
        }

        foreach ($data as $singleData) {
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

                $count = 0;
                foreach ($data_nilai as $key => $collect) {
                    if (!empty($data_nilai[$key + 1])) {
                        if (!empty($score_by_scale)) {
                            if ($score_by_scale['type'] == $data_nilai[$key]['type']) {
                                $score_by_scale['point'] += $collect['point'];
                                $score_by_scale['count'] = ++$count;
                            } else {
                                $final_score += ((($score_by_scale['point']) / $count) * $data_nilai[$key - 1]['scale']) / 100;
                                $count = 0;
                                $score_by_scale = [
                                    'type' => $collect['type'],
                                    'point' => $collect['point'],
                                    'count' => ++$count
                                ];
                            }
                        } else {
                            $score_by_scale = [
                                'type' => $collect['type'],
                                'point' => $collect['point'],
                                'count' => ++$count
                            ];
                        }
                    } else {
                        if (!empty($score_by_scale)) {
                            if ($score_by_scale['type'] == $data_nilai[$key]['type']) {
                                $score_by_scale['point'] += $collect['point'];
                                $score_by_scale['count'] = ++$count;
                            } else {
                                $final_score += ((($score_by_scale['point']) / $count) * $data_nilai[$key - 1]['scale']) / 100;
                            }
                        }
                        $final_score += ($collect['point'] * $data_nilai[$key]['scale']) / 100;
                    }
                }

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
        // dd($students);

        //return view('PDF.byClassBulk', ['data' => $students, 'guardian' => Teacher::where('id', ClassRoom::find($class_id)->guardian_id)->first()->name]);
        $pdf = PDF::loadView('PDF.byClassBulk', ['data' => $students, 'guardian' => Teacher::where('id', ClassRoom::find($class_id)->guardian_id)->first()->name])->setPaper('a3', 'portrait');

        return $pdf->download('Nilai_Siswa_' . ucfirst($students[0]['class']) . '.pdf');
    }
}
