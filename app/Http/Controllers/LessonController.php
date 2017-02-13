<?php
/**
 * Created by PhpStorm.
 * User: Aiden
 * Date: 2017/2/8
 * Time: 16:58
 */

namespace App\Http\Controllers;

use App\Http\Transformers\LessonTransformer;
use App\Exceptions\ApplicationException;
use App\Models\Lesson;
use App\Repositories\LessonRepository;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    protected $lessonTransformer;

    protected $lessonRepository;

    public function __construct(LessonTransformer $lessonTransformer, LessonRepository $lessonRepository)
    {
        $this->lessonTransformer = $lessonTransformer;
        $this->lessonRepository = $lessonRepository;
    }

    public function index()
    {
        $lessons = $this->lessonRepository->getAll();
        return $this->responseData($this->lessonTransformer->collection($lessons->toArray()));
    }

    public function query(Request $request)
    {
        $lesson = $this->lessonRepository->getOne($request->get('id'));
        if (!$lesson) {
            throw new ApplicationException(40800);
        }
        return $this->responseData([
            // 测试用字段
            'user' => $request->user(),
            'lesson' => $this->lessonTransformer->transform($lesson)
        ]);
    }

    public function create(Request $request)
    {
        if (!$request->get('title') or !$request->get('body')) {
            $options = [
                'time' => 3,
                'min' => 5
            ];
            return $this->setCode(40000)->responseError('test.word', $options);
        }
        $lesson = $request->user()->lessons()->create($request->all());
//        $lesson = Lesson::create($request->all());
        if (!$lesson) {
            throw new ApplicationException(50101);
        }
        return $this->responseData($this->lessonTransformer->transform($lesson));
    }

    public function update(Request $request)
    {
        $id = $request->get('id');
        $lesson = Lesson::find($id);
        if (!$lesson->update($request->all())) {
            throw new ApplicationException(50103);
        }
        return $this->responseData($this->lessonTransformer->transform($lesson));
    }

    public function delete(Request $request)
    {
        $ids = $request->get('ids');
        $lesson = Lesson::destroy($ids);
        if (!$lesson) {
            throw new ApplicationException(50102);
        }
        return $this->responseData([
            'count' => $lesson
        ]);
    }
}
