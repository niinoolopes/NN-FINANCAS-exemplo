import Cofre from '../src/pages/Cofre/Breadcrumb'
import Painel from '../src/pages/Painel/Breadcrumb'
import Config from '../src/pages/Config/Breadcrumb'
import Financa from '../src/pages/Financa/Breadcrumb'
import Investimento from '../src/pages/Investimento/Breadcrumb'

export default {
  ...Cofre,
  ...Painel,
  ...Config,
  ...Financa,
  ... Investimento
}