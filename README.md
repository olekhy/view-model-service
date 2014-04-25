View Model service
==================

echo json_decode(ViewModelRepo::getRepo()->addConcreteViewModel($callable)->getConcreteMode());

ViewModelRepo::getRepo()->getConcreteViewModel($callable, 'bbbb');
ViewModelRepo::getRepo()->getConcreteViewModel($callable, 'aaaa');

ViewModelRepo::getRepo()->getConcreteViewModel();
ViewModelRepo::getRepo()->getConcreteViewModel('bbbb');
ViewModelRepo::getRepo()->getConcreteViewModel('aaaa');
