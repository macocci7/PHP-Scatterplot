# PHP-Scatterplot

## Functions

### Class:Scatterplot extends Plotter
- data
- layers
- matrix

### Class:Plotter extends Analyzer
- plot
- regressionLine
- regressionCurve
- referenceLine
- specificationLimit
- create

### Class:Analyzer
- mean($data)
- variance($data)
- covariance($dataX, $dataY)
- standardDeviation($data)
- correlationCoefficient($dataX, $dataY)
- regressionLineFormula($dataX, $dataY)
- regressionCurveFormula: future version
- getUcl($data)
- getLcl($data)
- outliers($data)
